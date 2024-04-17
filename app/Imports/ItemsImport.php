<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Events\AfterBatch;
use Maatwebsite\Excel\Events\AfterChunk;

class ItemsImport implements ToModel, WithUpserts, WithHeadingRow, WithBatchInserts, WithChunkReading, WithEvents, WithProgressBar
{
    use Importable, RegistersEventListeners;

    private User $user;

    public function __construct(int $userId)
    {
        $this->user = User::findOrFail($userId);
    }


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (!$row['kod']) return null;

        return new Item([
            'code' => $row[Str::slug('Код')],
            'supplier_id' => $row[Str::slug('Поставщик')],
            'article_supplier' => $row[Str::slug('Артикул поставщика', '_')],
            'multiplicity' => $row[Str::slug('Кратность отгрузки', '_')],
            'brand' => $row[Str::slug('Бренд')],
            'article_manufacture' => $row[Str::slug('Артикул производителя', '_')],
            'user_id' => $this->user->id
        ]);
    }

    public function uniqueBy()
    {
        return ['user_id', 'code', 'ms_uuid'];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
