<?php

namespace Modules\Order\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Order\Models\NotChangeOzonState;

class NotChangeOzonStatesExport implements FromCollection, WithHeadings
{
    public function __construct(public int $userId)
    {
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return NotChangeOzonState::where('user_id', $this->userId)->get()->map(function (NotChangeOzonState $state) {
            return [
                'code' => $state->item->code,
                'delete' => 'Нет'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Код клиента',
            'Удалить'
        ];
    }
}
