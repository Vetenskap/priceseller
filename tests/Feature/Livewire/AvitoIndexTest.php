<?php

namespace Livewire;

use App\Livewire\Avito\AvitoIndex;
use App\Models\User;
use Tests\TestCase;

class AvitoIndexTest extends TestCase
{
    public function test_renders_successfully()
    {
        $user = User::first();

        $this->actingAs($user);

        $this->get('/avito')
            ->assertSeeLivewire(AvitoIndex::class);

        Livewire::test(AvitoIndex::class)
            ->assertStatus(200);
    }
}
