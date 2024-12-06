<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Reportable
{
    public function reports(): MorphMany;

    public function getUserId(): int;

    public function getTitle(): string;
}
