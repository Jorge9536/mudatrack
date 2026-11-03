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

class PersonalExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $choferes;
    protected $ayudantes;
    protected $resumen;
    protected $tipo;
    protected $fechaInicio;
    protected $fechaFin;

    // Colores corporativos
    const COLOR_TITULO    = '1E3A8A';
    const COLOR_RESUMEN   = 'F0F9FF';
    const COLOR_CHOFERES  = '2563EB';
    const COLOR_AYUDANTES = '0891B2';
    const COLOR_FILA_ALT  = 'F9FAFB';
    const COLOR_METRICA   = 'FEF3C7';

    public function __construct($choferes, $ayudantes, $resumen, $tipo, $fechaInicio, $fechaFin)
    {
        $this->choferes    = $choferes;
        $this->ayudantes   = $ayudantes;
        $this->resumen     = $resumen;
        $this->tipo        = $tipo;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin    = $fechaFin;
    }

    public function array(): array
    {
        $rows = [];

        // ============ TÍTULO ============
        $rows[] = ['REPORTE DE PERSONAL'];
        $rows[] = ['Período:', $this->fechaInicio . '  al  ' . $this->fechaFin];
        $rows[] = [];

        // ============ RESUMEN GENERAL ============
        $rows[] = ['RESUMEN GENERAL DEL PERÍODO'];
        $rows[] = ['Total Servicios', $this->resumen['total_servicios']];
        $rows[] = ['Total Horas Trabajadas', $this->resumen['total_horas'] . ' h'];
        $rows[] = ['Ingresos Totales', $this->resumen['total_ingresos']];
        $rows[] = ['Promedio Ingresos/Servicio', $this->resumen['promedio_ingresos']];
        $rows[] = ['Promedio Horas/Servicio', $this->resumen['promedio_horas'] . ' h'];
        $rows[] = [];

        // ============ CHOFERES ============
        if (in_array($this->tipo, ['todos', 'chofer'])) {
            $rows[] = ['CHOFERES'];
            $rows[] = [
                'Chofer', 'Teléfono', 'Servicios', 'Horas',
                'Ingresos (Bs)', 'Prom. h/serv', 'Vehículos', 'Ayudantes con los que trabajó',
            ];

            if ($this->choferes->isEmpty()) {
                $rows[] = ['Sin actividad en este período'];
            } else {
                foreach ($this->choferes as $ch) {
                    $rows[] = [
                        $ch['nombre'],
                        $ch['telefono'],
                        $ch['total_servicios'],
                        $ch['total_horas'],
                        $ch['total_ingresos'],
                        $ch['promedio_horas'],
                        implode(', ', $ch['vehiculos']) ?: '—',
                        implode(', ', $ch['ayudantes']) ?: '—',
                    ];
                }
            }

            $rows[] = [
                'TOTAL CHOFERES',
                $this->choferes->count() . ' activos',
                $this->choferes->sum('total_servicios'),
                round($this->choferes->sum('total_horas'), 1),
                $this->choferes->sum('total_ingresos'),
                '', '', '',
            ];
            $rows[] = [];

            // Detalle por servicio
            $rows[] = ['DETALLE DE SERVICIOS POR CHOFER'];
            $rows[] = [
                'Chofer', 'Servicio ID', 'Fecha', 'Horario', 'Horas',
                'Cliente', 'Origen', 'Destino', 'Vehículo',
                'Ayudantes', 'Costo (Bs)', 'Estado',
            ];

            foreach ($this->choferes as $ch) {
                foreach ($ch['servicios'] as $s) {
                    $rows[] = [
                        $ch['nombre'],
                        $s['id'],
                        $s['fecha'],
                        $s['hora_inicio'] . ' - ' . $s['hora_fin'],
                        $s['horas'],
                        $s['cliente'],
                        $s['origen'],
                        $s['destino'],
                        $s['vehiculo'],
                        implode(', ', $s['ayudantes']) ?: '—',
                        $s['costo'],
                        $s['estado'],
                    ];
                }
            }
            $rows[] = [];
        }

        // ============ AYUDANTES ============
        if (in_array($this->tipo, ['todos', 'ayudante'])) {
            $rows[] = ['AYUDANTES'];
            $rows[] = [
                'Ayudante', 'Teléfono', 'Servicios', 'Horas',
                'Ingresos (Bs)', 'Prom. h/serv', 'Vehículos', 'Choferes con los que trabajó',
            ];

            if ($this->ayudantes->isEmpty()) {
                $rows[] = ['Sin actividad en este período'];
            } else {
                foreach ($this->ayudantes as $ay) {
                    $rows[] = [
                        $ay['nombre'],
                        $ay['telefono'],
                        $ay['total_servicios'],
                        $ay['total_horas'],
                        $ay['total_ingresos'],
                        $ay['promedio_horas'],
                        implode(', ', $ay['vehiculos']) ?: '—',
                        implode(', ', $ay['choferes']) ?: '—',
                    ];
                }
            }

            $rows[] = [
                'TOTAL AYUDANTES',
                $this->ayudantes->count() . ' activos',
                $this->ayudantes->sum('total_servicios'),
                round($this->ayudantes->sum('total_horas'), 1),
                $this->ayudantes->sum('total_ingresos'),
                '', '', '',
            ];
            $rows[] = [];

            // Detalle por servicio
            $rows[] = ['DETALLE DE SERVICIOS POR AYUDANTE'];
            $rows[] = [
                'Ayudante', 'Servicio ID', 'Fecha', 'Horario', 'Horas',
                'Cliente', 'Origen', 'Destino', 'Vehículo', 'Chofer',
                'Costo (Bs)', 'Estado',
            ];

            foreach ($this->ayudantes as $ay) {
                foreach ($ay['servicios'] as $s) {
                    $rows[] = [
                        $ay['nombre'],
                        $s['id'],
                        $s['fecha'],
                        $s['hora_inicio'] . ' - ' . $s['hora_fin'],
                        $s['horas'],
                        $s['cliente'],
                        $s['origen'],
                        $s['destino'],
                        $s['vehiculo'],
                        $s['chofer'],
                        $s['costo'],
                        $s['estado'],
                    ];
                }
            }
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 24, 'B' => 14, 'C' => 11, 'D' => 10, 'E' => 15,
            'F' => 13, 'G' => 22, 'H' => 40, 'I' => 15, 'J' => 25,
            'K' => 14, 'L' => 14,
        ];
    }

    public function title(): string
    {
        return 'Personal';
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
                $sheet->mergeCells("A1:L1");
                $sheet->mergeCells("A2:L2");
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(18);

                $sheet->getStyle('A1:L1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_TITULO]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle('A2:L2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ===== RECORRER FILAS =====
                $seccionActual = null;
                $filaHeaderSeccion = null;
                $filaResumenActiva = false;

                for ($row = 3; $row <= $highestRow; $row++) {
                    $valorA = $sheet->getCell('A' . $row)->getValue();

                    // RESUMEN GENERAL
                    if ($valorA === 'RESUMEN GENERAL DEL PERÍODO') {
                        $sheet->mergeCells("A{$row}:L{$row}");
                        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                            'alignment' => ['indent' => 1, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(24);
                        $filaResumenActiva = true;
                        continue;
                    }

                    // Filas del resumen (6 filas siguientes)
                    if ($filaResumenActiva && $valorA !== null && $valorA !== '') {
                        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_RESUMEN]],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                        ]);
                        continue;
                    } else {
                        $filaResumenActiva = false;
                    }

                    // SECCIONES
                    if (in_array($valorA, ['CHOFERES', 'AYUDANTES'])) {
                        $seccionActual = $valorA;
                        $color = $valorA === 'CHOFERES' ? self::COLOR_CHOFERES : self::COLOR_AYUDANTES;

                        $sheet->mergeCells("A{$row}:L{$row}");
                        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                            'alignment' => ['indent' => 1, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(24);
                        $filaHeaderSeccion = $row + 1;
                        continue;
                    }

                    // SUBTÍTULOS
                    if (is_string($valorA) && str_starts_with($valorA, 'DETALLE DE')) {
                        $sheet->mergeCells("A{$row}:L{$row}");
                        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1F2937']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                            'alignment' => ['indent' => 1, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(22);
                        $filaHeaderSeccion = $row + 1;
                        continue;
                    }

                    // HEADERS DE TABLA
                    if ($filaHeaderSeccion === $row) {
                        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => true,
                            ],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(28);
                        continue;
                    }

                    // FILAS TOTAL
                    if (is_string($valorA) && str_starts_with($valorA, 'TOTAL')) {
                        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_METRICA]],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                        ]);
                        $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0.0');
                        $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                        continue;
                    }

                    // FILAS DE DATOS
                    if ($valorA !== null && $valorA !== '') {
                        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                            'font' => ['size' => 10],
                            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                        ]);

                        if (($row % 2) === 0) {
                            $sheet->getStyle("A{$row}:L{$row}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB(self::COLOR_FILA_ALT);
                        }

                        foreach (['C', 'D', 'F', 'J', 'K', 'L'] as $col) {
                            $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        }

                        $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0.0');
                        $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                        $sheet->getStyle("K{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                        $sheet->getStyle("E{$row}")->getFont()->setBold(true);
                    }
                }

                // Congelar título
                $sheet->freezePane('A3');
            },
        ];
    }
}