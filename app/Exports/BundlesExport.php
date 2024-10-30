<?php

namespace App\Exports;

use App\Imports\BundlesImport;
use App\Models\Bundle;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BundlesExport implements FromCollection, WithHeadings, WithStyles
{
    public function __construct(public User $user, public bool $template = false)
    {
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
    {
        if ($this->template) return collect();

        $allData = collect();

        $this->user->bundles()
            ->orderByDesc('updated_at')
            ->chunk(1000, function (Collection $items) use (&$allData) {
                $chunkData = $items->map(function (Bundle $item) {
                    $main = [
                        'ms_uuid' => $item->ms_uuid,
                        'code' => $item->code,
                        'name' => $item->name,
                        'updated_at' => $item->updated_at,
                        'created_at' => $item->created_at,
                        'delete' => 'Нет'
                    ];

                    return $main;

                });

                $allData = $allData->merge($chunkData);
            });


        return $allData;
    }

    public function headings(): array
    {
        $main = BundlesImport::HEADERS;

        return $main;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
    }
}
