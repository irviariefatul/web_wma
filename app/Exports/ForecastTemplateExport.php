<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromArray;

class ForecastTemplateExport implements FromArray, WithHeadings, WithColumnFormatting
{
    public function array(): array
    {
        // Menyediakan contoh data
        return [
            ['1/2/2024', '0', '0', '0', '0', 7323.59, '0'],
            ['1/3/2024', '0', '0', '0', '0', 7279.09, '0'],
            ['1/4/2024', '0', '0', '0', '0', 7359.76, '0'],
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Open',
            'High',
            'Low',
            'Close',
            'Adj Close',
            'Volume',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_YYYYMMDD2, // Menggunakan format tanggal pendek YYYY-MM-DD
        ];
    }
}
