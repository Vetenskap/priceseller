<?php

namespace Modules\SamsonApi\HttpClient\Resources;

use Illuminate\Support\Collection;
use Modules\SamsonApi\HttpClient\SamsonClient;

class SkuList
{
    protected ?string $previous;
    protected ?int $next = 1;
    protected string $apiKey;
    protected int $paginationCount = 1000;

    public function __construct(string $apiKey, $paginationCount = 1000, $next = 1)
    {
        $this->apiKey = $apiKey;
        $this->paginationCount = $paginationCount;
        $this->next = $next;
    }


    public function fetchNext(): Collection
    {
        $queryParameters = [
            'api_key' => $this->apiKey,
            'pagination_count' => $this->paginationCount,
            'pagination_page' => $this->next
        ];

        $client = new SamsonClient($this->apiKey);
        $list = $client->get(Sku::ENDPOINT, $queryParameters);

        $url = $list->toCollectionSpread()->get('meta')->get('pagination')->get('next');
        $url = substr($url, strrpos($url, '?') + 1);
        parse_str($url, $params);

        $this->next = (int) collect($params)->get('pagination_page');
        $this->previous = $list->toCollectionSpread()->get('meta')->get('pagination')->get('previous');

        return collect($list->get('data'))->map(function (array $sku) {
            return new Sku(collect($sku));
        });
    }

    public function hasNext(): bool
    {
        return (bool) $this->next;
    }
}
