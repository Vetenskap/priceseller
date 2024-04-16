<?php

namespace Livewire;

use App\Livewire\Email\EmailIndex;
use App\Models\User;
use Tests\TestCase;

class EmailIndexTest extends TestCase
{
    public function test_renders_successfully()
    {
        $user = User::first();

        $this->actingAs($user);

        $this->get('/emails')
            ->assertSeeLivewire(EmailIndex::class);

        Livewire::test(EmailIndex::class)
            ->assertViewHas('emails', function ($emails) {
                return count($emails) === 1;
            });
    }
}
