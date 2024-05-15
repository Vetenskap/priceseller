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

class ItemsImport implements ToModel, WithUpserts, WithBatchInserts, WithChunkReading, WithEvents, WithProgressBar
{
    use Importable, RegistersEventListeners;

    private User $user;

    public array $suppliers = [
        'ООО "ШАТЕ-М ПЛЮС"' => '9bd1f334-9270-429e-b225-8382d3f16ba9',
        'ООО "ГРИНЛАЙТ"' => '9bd1f334-9270-429e-b335-8382d3f27ba9',
        'ООО Берг' => '9bd1f334-9270-429e-b335-8382d3f16ba9',
    ];

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
        return new Item([
            'code' => $row[3],
            'supplier_id' => $row[Str::slug('Поставщик')],
            'article' => $row[71],
            'multiplicity' => (int)preg_replace("/[^0-9]/", "", $row[73]),
            'brand' => $row[68],
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
