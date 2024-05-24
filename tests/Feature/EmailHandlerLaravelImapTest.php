<?php

namespace Tests\Feature;

use App\Components\EmailClient\EmailHandlerLaravelImap;
use App\Models\Email;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmailHandlerLaravelImapTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $user = User::findOrFail(1);

        /** @var Email $email */
        foreach ($user->emails as $email) {
            $handler = new EmailHandlerLaravelImap($email->address, $email->password);
            $email->suppliers->each(function (Supplier $supplier) use ($handler) {
                $path = $handler->getNewPrice($supplier->pivot->email, $supplier->pivot->filename, 'SEEN');
                $this->assertNotEmpty($path);
                $this->assertTrue(Storage::disk('public')->exists($path));
                Storage::disk('public')->delete($path);
            });
        }
    }
}
