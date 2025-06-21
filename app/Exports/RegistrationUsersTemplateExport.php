<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RegistrationUsersTemplateExport implements FromArray, WithHeadings, WithStyles
{
    protected $registration;

    public function __construct($registration)
    {
        $this->registration = $registration;
    }

    public function array(): array
    {
        // Return empty array for template (just headers)
        return [
            array_merge(
                [
                    'John',
                    'Doe',
                    'john.doe@example.com',
                    '+1234567890',
                ],
                array_fill(0, $this->registration->dynamicFormFields->count(), 'Sample Data')
            )
        ];
    }

    public function headings(): array
    {
        $headings = [
            'first_name',
            'last_name',
            'email',
            'phone'
        ];

        // Add dynamic field headings
        foreach ($this->registration->dynamicFormFields as $field) {
            $headings[] = strtolower(str_replace(' ', '_', $field->label));
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}
