<?php

namespace App\Imports;

use App\Models\Bundle;
use App\Models\User;
use App\Services\Bundle\BundleService;
use App\Services\ItemsImportReportService;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class BundlesImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    CONST HEADERS = [
        'МС UUID',
        'Код',
        'Наименование',
        'Обновлён',
        'Создан',
        'Удалить'
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
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $row = collect($row);

        if ($bundle = $this->user->bundles()->where('code', $row->get('Код'))->first()) {

            if ($row->get('Удалить')) {

                $this->deleted++;

                $bundle->delete();

                return null;
            }

            $this->updated++;

            $bundle->update([
                'ms_uuid' => $row->get('МС UUID'),
                'name' => $row->get('Наименование'),
            ]);

            return null;
        }

        if ($row->get('Удалить')) {

            $this->error++;

            ItemsImportReportService::addBadItem(
                $this->user,
                0,
                'Удалить',
                ['Не удалось создать товар, стоит метка "Удалить"'],
                $row->all()
            );

            return null;
        }

        $this->correct++;

        return new Bundle([
            'ms_uuid' => $row->get('МС UUID'),
            'code' => $row->get('Код'),
            'name' => $row->get('Наименование'),
            'user_id' => $this->user->id,
            'id' => Str::uuid(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        if ($index % 1000 === 0) ItemsImportReportService::flush($this->user, $this->correct, $this->error, $this->updated, $this->deleted);

        $data['Удалить'] = $data['Удалить'] === 'Да';

        return $data;
    }

    public function rules(): array
    {
        return BundleService::excelImportRules();
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
