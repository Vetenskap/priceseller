<?php

namespace App\Imports;

use App\Models\User;
use App\Services\Bundle\BundleItemsService;
use App\Services\ItemsImportReportService;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class BundleItemsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    CONST HEADERS = [
        'Код комплекта',
        'Код товара',
        'Кратность отгрузки',
        'Открепить'
    ];

    public int $correct = 0;
    public int $error = 0;
    public int $updated = 0;
    public int $deleted = 0;

    public function __construct(public User $user)
    {

    }

    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row): ?Model
    {
        $row = collect($row);

        $bundle = $this->user->bundles()->where('code', $row->get('Код комплекта'))->first();
        $item = $this->user->items()->where('code', $row->get('Код товара'))->first();

        if (!$bundle) {

            $this->error++;

            ItemsImportReportService::addBadItem(
                $this->user,
                0,
                'Код комплекта',
                ['Комплект не найден'],
                $row->all()
            );

            return null;
        }

        if (!$item) {

            $this->error++;

            ItemsImportReportService::addBadItem(
                $this->user,
                0,
                'Код товара',
                ['Товар не найден'],
                $row->all()
            );

            return null;
        }

        if ($row->get('Открепить')) {

            $this->deleted++;

            $bundle->items()->detach($item->id);

            return null;
        }

        if ($supplierBundle = $bundle->items()->first()?->supplier) {
            if ($supplierBundle->id !== $item->supplier_id) {
                $this->error++;
                ItemsImportReportService::addBadItem(
                    $this->user,
                    0,
                    'Код товар',
                    ['В комплекте не может быть товары другого поставщика'],
                    $row->all()
                );
                return null;
            }
        }

        $this->correct++;

        $bundle->items()->attach($item->id, ['multiplicity' => $row->get('Кратность отгрузки')]);

        return null;
    }

    public function prepareForValidation($data, $index)
    {
        if ($index % 1000 === 0) ItemsImportReportService::flush($this->user, $this->correct, $this->error, $this->updated, $this->deleted);

        $data['Открепить'] = $data['Открепить'] === 'Да';

        return $data;
    }

    public function rules(): array
    {
        return BundleItemsService::excelImportRules();
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {

            $this->error++;

            ItemsImportReportService::addBadItem(
                $this->user,
                $failure->row(),
                $failure->attribute(),
                $failure->errors(),
                $failure->values()
            );

        }
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
