<?php

namespace App\Exports;

use App\Imports\BundleItemsImport;
use App\Imports\BundlesImport;
use App\Models\Bundle;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BundleItemsExport implements FromCollection, WithHeadings, WithStyles
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
            ->chunk(1000, function (Collection $bundles) use (&$allData) {
                $chunkData = $bundles->map(function (Bundle $bundle) {
                    return $bundle->items->map(function (Item $item) use ($bundle) {
                        return [
                            'code_bundle' => $bundle->code,
                            'code_item' => $item->code,
                            'multiplicity' => $item->pivot->multiplicity,
                            'detach' => 'Нет'
                        ];
                    });
                });

                $allData = $allData->merge($chunkData);
            });


        return $allData;
    }

    public function headings(): array
    {
        $main = BundleItemsImport::HEADERS;

        return $main;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
    }
}
