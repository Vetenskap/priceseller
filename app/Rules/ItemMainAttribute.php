<?php

namespace App\Rules;

use App\Models\Item;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ItemMainAttribute implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (collect(Item::MAINATTRIBUTES)->where('name', $value)->isEmpty()) {
            $fail('Такого атрибута нет в списке основных атрибутов');
        }
    }
}
