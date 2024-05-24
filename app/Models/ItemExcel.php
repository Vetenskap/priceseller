<?php

namespace App\Models;

class ItemExcel
{
    public string $uuid;
    public string $name;
    public string $supplier_name;
    public string $code;
    public string $brand;
    public string $article_supplier;
    public int $multiplicity;

    public array $suppliers = [
        'ООО "ШАТЕ-М ПЛЮС"' => '9bd1f334-9270-429e-b225-8382d3f16ba9',
        'ООО "ГРИНЛАЙТ"' => '9bd1f334-9270-429e-b335-8382d3f27ba9',
        'ООО Берг' => '9bd1f334-9270-429e-b335-8382d3f16ba9',
    ];

    public function __construct(array $data)
    {
        $this->uuid = $data[1];
        $this->code = $data[3];
        $this->name = $data[4];
        $this->supplier_name = $data[46] === '' ? "Неизвестный поставщик" : $data[42];
        $this->article_supplier = $data[71];
        $this->brand = $data[68];
        $this->multiplicity = (int)preg_replace("/[^0-9]/", "", $data[73]);
    }

    public function toArray(): array
    {
        return [
            'ms_uuid' => $this->uuid,
            'name' => $this->name,
            'supplier_id' => $this->suppliers[$this->supplier_name] ?? throw new \Exception('Поставщик не найден'),
            'code' => $this->code,
            'article' => $this->article_supplier,
            'multiplicity' => $this->multiplicity,
            'brand' => $this->brand,
            'user_id' => 1
        ];
    }
}
