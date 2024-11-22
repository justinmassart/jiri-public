<?php

namespace Tests\Feature\Contacts;

use App\Models\Contact;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('ensures that an authenticated user can delete one of his own contact', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create(['user_id' => $user->id]);

    $response = actingAs($user)->get('/contacts/'.$contact->slug);

    $response->assertOk();
    $response->assertSee($contact->firstname.' '.$contact->lastname);

    $response->assertSee($contact->email);

    $response->assertSee('Sauvegarder');
    $response->assertSee('Supprimer');

    Livewire::test(ShowContact::class, ['contact' => $contact])
        ->call('deleteContact');

    expect(Contact::where('id', $contact->id)->exists())->toBeFalse();
});
