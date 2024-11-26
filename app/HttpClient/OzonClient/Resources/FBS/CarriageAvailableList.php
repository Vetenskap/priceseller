<?php

namespace App\HttpClient\OzonClient\Resources\FBS;

class CarriageAvailableList
{
    const ENDPOINT = '/v1/posting/carriage-available/list';



    protected int $delivery_method_id = 0;
    protected string $departure_date;

    public function __construct()
    {
        $this->departure_date = now()->endOfDay()->toRfc3339String();
    }
}
