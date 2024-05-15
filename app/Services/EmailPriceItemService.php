<?php

namespace App\Services;

use App\Models\EmailPriceItem;

class EmailPriceItemService
{
    public static function handleFoundItem(string $supplierId, string $article, string $brand, float $price, string $stock, string $itemId): void
    {
        EmailPriceItem::updateOrCreate([
            'supplier_id' => $supplierId,
            'article' => $article,
            'brand' => $brand
        ], [
            'supplier_id' => $supplierId,
            'article' => $article,
            'brand' => $brand,
            'price' => $price,
            'stock' => $stock,
            'message' => 'Товар найден',
            'status' => 0,
            'item_id' => $itemId
        ]);
    }

    public static function handleNotFoundItem(string $supplierId, string $article, string $brand, float $price, string $stock): void
    {
        EmailPriceItem::updateOrCreate([
            'supplier_id' => $supplierId,
            'article' => $article,
            'brand' => $brand
        ], [
            'supplier_id' => $supplierId,
            'article' => $article,
            'brand' => $brand,
            'price' => $price,
            'stock' => $stock,
            'message' => 'Товар не найден',
            'status' => 1
        ]);
    }
}
