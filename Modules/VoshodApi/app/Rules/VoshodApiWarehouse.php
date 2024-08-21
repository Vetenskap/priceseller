<?php

namespace Modules\VoshodApi\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VoshodApiWarehouse implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (collect(config('voshodapi.warehouses'))->where('name', $value)->isEmpty()) {
            $fail('Склад не существует');
        }
    }
}
