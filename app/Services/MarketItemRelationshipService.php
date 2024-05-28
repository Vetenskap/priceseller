<?php

namespace App\Services;

use App\Models\MarketItemRelationship;

class MarketItemRelationshipService
{
    public static function handleFoundItem(string $externalCode, string $code, string $marketId, string $marketType): void
    {
        MarketItemRelationship::updateOrCreate([
            'external_code' => $externalCode,
            'relationshipable_id' => $marketId,
            'relationshipable_type' => $marketType
        ], [
            'external_code' => $externalCode,
            'code' => $code,
            'status' => 0,
            'message' => 'Связь создана',
            'relationshipable_id' => $marketId,
            'relationshipable_type' => $marketType
        ]);
    }

    public static function handleNotFoundItem(string $externalCode, string $marketId, string $marketType, ?string $code = null): void
    {
        MarketItemRelationship::updateOrCreate([
            'external_code' => $externalCode,
            'relationshipable_id' => $marketId,
            'relationshipable_type' => $marketType
        ], [
            'external_code' => $externalCode,
            'code' => $code,
            'status' => 1,
            'message' => 'Не удалось создать связь',
            'relationshipable_id' => $marketId,
            'relationshipable_type' => $marketType
        ]);
    }

    public static function handleItemWithMessage(string $externalCode, string $marketId, string $marketType, string $code, string $message): void
    {
        MarketItemRelationship::updateOrCreate([
            'external_code' => $externalCode,
            'relationshipable_id' => $marketId,
            'relationshipable_type' => $marketType
        ], [
            'external_code' => $externalCode,
            'code' => $code,
            'status' => 1,
            'message' => $message,
            'relationshipable_id' => $marketId,
            'relationshipable_type' => $marketType
        ]);
    }
}
