<?php

namespace Modules\Moysklad\HttpClient\Resources\Objects;

use Illuminate\Support\Collection;

class Alcoholic
{
    public ?bool $excised = null;
    public ?int $type = null;
    public ?float $strength = null;
    public ?float $volume = null;

    public function __construct(Collection $alcoholic)
    {
        $this->excised = $alcoholic->get('excised');
        $this->type = $alcoholic->get('type');
        $this->strength = $alcoholic->get('strength');
        $this->volume = $alcoholic->get('volume');
    }

    public function toArray(): array
    {
        return [
            'excised' => $this->excised,
            'type' => $this->type,
            'strength' => $this->strength,
            'volume' => $this->volume
        ];
    }
}
