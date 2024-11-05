<?php

namespace App\HttpClient\WbClient\Resources\Card;

use App\HttpClient\WbClient\WbClient;
use Illuminate\Support\Collection;

class CardList
{
    const ENDPOINT = 'https://suppliers-api.wildberries.ru/content/v2/get/cards/list';
    protected bool $sort_ascending = false;
    protected int $filter_with_photo = -1;
    protected string $filter_text_search = '';
    protected array $filter_tag_ids = [];
    protected array $filter_allowed_categories_only = [];
    protected array $filter_object_ids = [];
    protected array $filter_brands = [];
    protected int $filter_imt_id = 0;
    protected int $cursor_limit = 100;
    protected string $cursor_updated_at = '';
    protected int $cursor_nm_id = 0;
    protected int $cursor_total = 0;
    protected string $api_key;

    public function __construct(string $api_key)
    {
        $this->api_key = $api_key;
    }

    public function next(): Collection
    {
        $data = array(
            "settings" => array(
                "sort" => array(
                    "ascending" => $this->sort_ascending
                ),
                "filter" => array(
                    "withPhoto" => $this->filter_with_photo,
                    "textSearch" => $this->filter_text_search,
                    "tagIds" => $this->filter_tag_ids,
                    "allowedCategoriesOnly" => $this->filter_allowed_categories_only,
                    "objectIds" => $this->filter_object_ids,
                    "brands" => $this->filter_brands,
                    "imtId" => $this->filter_imt_id
                ),
                "cursor" => array(
                    "limit" => $this->cursor_limit,
                    "updatedAt" => $this->cursor_updated_at,
                    "nmId" => $this->cursor_nm_id
                )
            ),
        );

        $data = $this->filterArray($data);

        $client = new WbClient($this->api_key);
        $result = $client->post(static::ENDPOINT, $data)->collect()->toCollectionSpread();
        $this->cursor_updated_at = $result->get('cursor')->get('updatedAt');
        $this->cursor_nm_id = $result->get('cursor')->get('nmID');
        $this->cursor_total = $result->get('cursor')->get('total');

        return $result->get('cards')->map(function (Collection $card) {
             return new Card($card);
        });
    }

    private function filterArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->filterArray($value);

                if (empty($array[$key])) {
                    unset($array[$key]);
                }
            } elseif (!$value && !is_bool($value)) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    public function hasNext(): bool
    {
        return $this->cursor_total >= $this->cursor_limit;
    }

}
