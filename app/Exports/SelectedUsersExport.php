<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SelectedUsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $users;
    protected $event;

    public function __construct($users, $event)
    {
        $this->users = $users;
        $this->event = $event;
    }

    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Registration Form',
            'User Type',
            'Status',
            'Registration Code',
            'Registered Date',
            'Conference',
            'Profession',
            'Country',
            'City',
            'Custom Fields'
        ];
    }

    public function map($user): array
    {
        // Get custom field values
        $customFields = [];
        foreach ($user->formFieldValues as $fieldValue) {
            $customFields[] = $fieldValue->field->label . ': ' . $fieldValue->value;
        }

        return [
            $user->id,
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->phone ?? 'N/A',
            $user->registration->name ?? 'N/A',
            $user->userType->name ?? 'N/A',
            ucfirst($user->status),
            $user->unique_code ?? 'N/A',
            $user->created_at->format('Y-m-d H:i:s'),
            $user->conference->name ?? 'N/A',
            $user->profession->name ?? 'N/A',
            $user->country->name ?? 'N/A',
            $user->city ?? 'N/A',
            implode('; ', $customFields)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
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
            // Style all data rows
            'A2:O' . ($this->users->count() + 1) => [
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
        return [
            'A' => 8,   // ID
            'B' => 15,  // First Name
            'C' => 15,  // Last Name
            'D' => 25,  // Email
            'E' => 15,  // Phone
            'F' => 20,  // Registration Form
            'G' => 15,  // User Type
            'H' => 12,  // Status
            'I' => 15,  // Registration Code
            'J' => 18,  // Registered Date
            'K' => 15,  // Conference
            'L' => 15,  // Profession
            'M' => 15,  // Country
            'N' => 15,  // City
            'O' => 30,  // Custom Fields
        ];
    }
}
