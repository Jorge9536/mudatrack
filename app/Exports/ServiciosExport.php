<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ServiciosExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $servicios;
    protected $fechaInicio;
    protected $fechaFin;

    const COLOR_TITULO   = '1E3A8A';
    const COLOR_HEADER   = '3B82F6';
    const COLOR_FILA_ALT = 'F9FAFB';
    const COLOR_TOTAL    = 'FEF3C7';

    public function __construct($servicios, $fechaInicio, $fechaFin)
    {
        $this->servicios   = $servicios;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin    = $fechaFin;
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['REPORTE DE SERVICIOS'];
        $rows[] = ['Período:', $this->fechaInicio . '  al  ' . $this->fechaFin];
        $rows[] = [];

        $rows[] = [
            'ID', 'Cliente', 'Teléfono', 'Origen', 'Destino',
            'Fecha', 'Costo (Bs)', 'Estado', 'Pago',
        ];

        foreach ($this->servicios as $s) {
            $rows[] = [
                $s->id,
                $s->cliente->nombre_completo ?? '—',
                $s->cliente->telefono ?? '—',
                $s->origen,
                $s->destino,
                optional($s->fecha_servicio)->format('d/m/Y'),
                (float) $s->costo_total,
                $s->estado_label ?? $s->estado,
                $s->estado_pago === 'pagado' ? 'Pagado' : 'Pendiente',
            ];
        }

        // Totales
        $rows[] = [];
        $rows[] = [
            'TOTALES', '', '', '', '', '',
            (float) $this->servicios->sum('costo_total'),
            $this->servicios->count() . ' registros',
            '',
        ];

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 25,
            'C' => 14,
            'D' => 30,
            'E' => 30,
            'F' => 12,
            'G' => 14,
            'H' => 14,
            'I' => 12,
        ];
    }

    public function title(): string
    {
        return 'Servicios';
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Merge título
                $sheet->mergeCells('A1:I1');
                $sheet->mergeCells('A2:I2');
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // Título
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_TITULO]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Subtítulo período
                $sheet->getStyle('A2:I2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Header (fila 4)
                $headerRow = 4;
                $sheet->getStyle("A{$headerRow}:I{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_HEADER]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(22);

                // Filas de datos
                $dataStart = $headerRow + 1;
                $dataEnd   = $highestRow - 2; // -2 por fila vacía + totales

                for ($row = $dataStart; $row <= $dataEnd; $row++) {
                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'font' => ['size' => 10],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                    ]);

                    if (($row % 2) === 0) {
                        $sheet->getStyle("A{$row}:I{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB(self::COLOR_FILA_ALT);
                    }

                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Monto en negrita y alineado a la derecha
                    $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                }

                // Fila de totales
                $totalRow = $highestRow;
                $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_TOTAL]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                ]);
                $sheet->getStyle("G{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                // Congelar primeras 4 filas
                $sheet->freezePane('A5');

                // Auto filtro
                $sheet->setAutoFilter("A{$headerRow}:I{$headerRow}");
            },
        ];
    }
}