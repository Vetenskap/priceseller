<?php

namespace Modules\VoshodApi\HttpClient\Resources;

use Illuminate\Support\Collection;
use Modules\VoshodApi\HttpClient\VoshodClient;

class ItemsPageList
{
    protected int $current;
    protected ?int $next;
    protected ?int $prev;
    protected int $pages;
    protected int $items;
    protected string $apiKey;
    protected string $proxyIp;
    protected int $proxyPort;
    protected string $proxyLogin;
    protected string $proxyPassword;

    public function __construct(string $apiKey, string $proxyIp, int $proxyPort, string $proxyLogin, string $proxyPassword, int $next = 1)
    {
        $this->next = $next;
        $this->apiKey = $apiKey;
        $this->proxyIp = $proxyIp;
        $this->proxyPort = $proxyPort;
        $this->proxyLogin = $proxyLogin;
        $this->proxyPassword = $proxyPassword;
    }

    public function fetchNext(): Collection
    {
        $client = new VoshodClient($this->apiKey, $this->proxyLogin, $this->proxyPassword, $this->proxyIp, $this->proxyPort);
        $result = $client->get('/items.json', ['page' => $this->next, 'a' => 1]);

        $page = $result->get('response')->get('page');
        $this->current = $page->get('current');
        $this->next = $page->get('next');
        $this->prev = $page->get('prev');
        $this->items = $page->get('items');

        return $result->get('response')->get('items')->map(function (Collection $item) {
            return new Item($item);
        });
    }

    public function hasNext(): bool
    {
        return !is_null($this->next);
    }

}
