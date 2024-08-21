<?php

namespace Modules\BergApi\HttpClient\Resources;

use Illuminate\Support\Collection;

class Resource
{
    const ENDPOINT = '/ordering/get_stock.json';
    const ATTRIBUTES = [
          ['name' => 'id', 'label' => 'Внутренний идентификатор'],
          ['name' => 'name', 'label' => 'Наиманование товара'],
          ['name' => 'article', 'label' => 'Артикул товара'],
          ['name' => 'brand_id', 'label' => 'Идентификатор бренда'],
          ['name' => 'brand_name', 'label' => 'Наименование бренда'],
    ];

    protected int $id;
    protected string $article;
    protected int $brand_id;
    protected string $brand_name;
    protected string $name;
    protected Collection $offers;


    public function __construct(Collection $resource)
    {
        $this->id = $resource->get('id');
        $this->article = $resource->get('article');
        $this->brand_id = $resource->get('brand')->get('id');
        $this->brand_name = $resource->get('brand')->get('name');
        $this->name = $resource->get('name');

        $this->offers = collect();

        foreach ($resource->get('offers') as $offer) {
            $this->offers->push(new Offer($offer));
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArticle(): string
    {
        return $this->article;
    }

    public function getBrandId(): int
    {
        return $this->brand_id;
    }

    public function getBrandName(): string
    {
        return $this->brand_name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOffers(): Collection
    {
        return $this->offers;
    }


}
