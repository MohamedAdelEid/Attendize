<?php

namespace App\Exports;

use App\Services\RegistrationUsersExportColumns;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SelectedUsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $users;
    protected $event;
    protected $columns;
    protected $registrationId;
    protected $userTypeOptionNames;
    protected $columnLabels;

    public function __construct(
        $users,
        $event,
        array $columns,
        ?int $registrationId = null,
        array $userTypeOptionNames = [],
        array $columnLabels = []
    ) {
        $this->users = $users;
        $this->event = $event;
        $this->columns = $columns;
        $this->registrationId = $registrationId;
        $this->userTypeOptionNames = $userTypeOptionNames;
        $this->columnLabels = $columnLabels;
    }

    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        return RegistrationUsersExportColumns::headingsForColumns(
            $this->columns,
            $this->event->id,
            $this->registrationId,
            $this->columnLabels
        );
    }

    public function map($user): array
    {
        $row = [];
        foreach ($this->columns as $column) {
            $row[] = RegistrationUsersExportColumns::valueForColumn(
                $column,
                $user,
                $this->userTypeOptionNames
            );
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = Coordinate::stringFromColumnIndex(count($this->columns));
        $lastRow = $this->users->count() + 1;

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            'A2:' . $lastColumn . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        $widths = [];
        foreach ($this->columns as $index => $column) {
            $letter = Coordinate::stringFromColumnIndex($index + 1);
            $widths[$letter] = strpos($column, 'field_') === 0 ? 24 : 18;
        }

        return $widths;
    }
}
