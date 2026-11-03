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
use Carbon\Carbon;

class MorososExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $morosos;

    // Paleta con rojo
    const COLOR_TITULO   = '991B1B'; // Rojo oscuro
    const COLOR_HEADER   = 'DC2626'; // Rojo
    const COLOR_FILA_ALT = 'FEF2F2'; // Rojo muy claro
    const COLOR_TOTAL    = 'FEE2E2'; // Rojo claro
    const COLOR_RESUMEN  = 'FEF2F2';

    public function __construct($morosos)
    {
        $this->morosos = $morosos;
    }

    public function array(): array
    {
        $rows = [];

        // Título
        $rows[] = ['REPORTE DE CLIENTES MOROSOS'];
        $rows[] = ['Generado:', now()->format('d/m/Y H:i')];
        $rows[] = [];

        // Resumen
        $totalClientes = $this->morosos->count();
        $totalDeudas   = $this->morosos->sum(fn($c) => $c->deudas->where('estado', 'pendiente')->count());
        $totalMonto    = $this->morosos->sum(fn($c) => $c->deudas->where('estado', 'pendiente')->sum('monto'));

        $rows[] = ['RESUMEN'];
        $rows[] = ['Clientes morosos', $totalClientes];
        $rows[] = ['Total deudas pendientes', $totalDeudas];
        $rows[] = ['Total adeudado (Bs)', (float) $totalMonto];
        $rows[] = [];

        // Encabezado principal
        $rows[] = [
            'Cliente',
            'Teléfono',
            'Servicio ID',
            'Fecha servicio',
            'Horario',
            'Origen',
            'Destino',
            'Vehículo',
            'Chofer',
            'Ayudantes',
            'Vencimiento',
            'Días',
            'Monto (Bs)',
            'Estado',
        ];

        foreach ($this->morosos as $cliente) {
            $deudas = $cliente->deudas->where('estado', 'pendiente');

            foreach ($deudas as $deuda) {
                $servicio = $deuda->servicio;

                $horario = '—';
                if ($servicio && $servicio->hora_inicio && $servicio->hora_fin) {
                    $horario = Carbon::parse($servicio->hora_inicio)->format('H:i')
                             . ' - '
                             . Carbon::parse($servicio->hora_fin)->format('H:i');
                }

                $dias = now()->diffInDays($deuda->fecha_vencimiento, false);
                $vencida = $deuda->estaVencida();

                $rows[] = [
                    $cliente->nombre_completo,
                    $cliente->telefono ?? '—',
                    $servicio->id ?? '—',
                    $servicio ? optional($servicio->fecha_servicio)->format('d/m/Y') : '—',
                    $horario,
                    $servicio->origen ?? '—',
                    $servicio->destino ?? '—',
                    $servicio->vehiculo->placa ?? '—',
                    $servicio->chofer->nombre_completo ?? '—',
                    ($servicio && $servicio->ayudantes->isNotEmpty())
                        ? $servicio->ayudantes->pluck('nombre_completo')->join(', ')
                        : '—',
                    $deuda->fecha_vencimiento->format('d/m/Y'),
                    $vencida ? 'Vencida (' . abs($dias) . 'd)' : $dias . 'd',
                    (float) $deuda->monto,
                    $vencida ? 'Vencida' : 'Pendiente',
                ];
            }
        }

        // Totales
        $rows[] = [];
        $rows[] = [
            'TOTALES',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            (float) $totalMonto,
            '',
        ];

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 24, 'B' => 14, 'C' => 10, 'D' => 14, 'E' => 16,
            'F' => 28, 'G' => 28, 'H' => 14, 'I' => 22, 'J' => 32,
            'K' => 14, 'L' => 14, 'M' => 14, 'N' => 12,
        ];
    }

    public function title(): string
    {
        return 'Morosos';
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

                // ===== TÍTULO =====
                $sheet->mergeCells('A1:N1');
                $sheet->mergeCells('A2:N2');
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(18);

                $sheet->getStyle('A1:N1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_TITULO]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2:N2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ===== SECCIONES =====
                $headerRow = null;

                for ($row = 3; $row <= $highestRow; $row++) {
                    $valorA = $sheet->getCell('A' . $row)->getValue();

                    // RESUMEN
                    if ($valorA === 'RESUMEN') {
                        $sheet->mergeCells("A{$row}:N{$row}");
                        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_HEADER]],
                            'alignment' => ['indent' => 1, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(22);
                        continue;
                    }

                    // Filas del resumen
                    if (in_array($valorA, ['Clientes morosos', 'Total deudas pendientes', 'Total adeudado (Bs)'])) {
                        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_RESUMEN]],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FECACA']]],
                        ]);
                        if ($valorA === 'Total adeudado (Bs)') {
                            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                            $sheet->getStyle("B{$row}")->getFont()->getColor()->setRGB('DC2626');
                        }
                        continue;
                    }

                    // Header de tabla
                    if ($valorA === 'Cliente') {
                        $headerRow = $row;
                        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_HEADER]],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical'   => Alignment::VERTICAL_CENTER,
                                'wrapText'   => true,
                            ],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FCA5A5']]],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(28);
                        continue;
                    }

                    // FILA TOTAL
                    if ($valorA === 'TOTALES') {
                        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '991B1B']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_TOTAL]],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FCA5A5']]],
                        ]);
                        $sheet->getStyle("M{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                        continue;
                    }

                    // Filas de datos
                    if ($valorA !== null && $valorA !== '' && $headerRow && $row > $headerRow) {
                        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                            'font' => ['size' => 10],
                            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FEE2E2']]],
                        ]);

                        // Zebra rojo claro
                        if (($row % 2) === 0) {
                            $sheet->getStyle("A{$row}:N{$row}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB(self::COLOR_FILA_ALT);
                        }

                        // Alineaciones
                        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("N{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        // Monto en rojo negrita
                        $sheet->getStyle("M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("M{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                        $sheet->getStyle("M{$row}")->getFont()->setBold(true);
                        $sheet->getStyle("M{$row}")->getFont()->getColor()->setRGB('DC2626');

                        // Marcar vencidas con rojo
                        if ($sheet->getCell("N{$row}")->getValue() === 'Vencida') {
                            $sheet->getStyle("N{$row}")->getFont()->getColor()->setRGB('DC2626');
                            $sheet->getStyle("N{$row}")->getFont()->setBold(true);
                        }
                    }
                }

                // Congelar título
                $sheet->freezePane('A3');
            },
        ];
    }
}