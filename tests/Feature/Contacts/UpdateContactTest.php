<?php

namespace Tests\Feature\Contacts;

use App\Livewire\Contacts\ShowContact;
use App\Models\Contact;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('ensures that an authenticated user can update the infos of a contact in the edit form', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create(['user_id' => $user->id]);

    $response = actingAs($user)->get('/contacts/'.$contact->slug);

    $response->assertOk();
    $response->assertSee($contact->firstname.' '.$contact->lastname);

    $response->assertSee($contact->email);

    $response->assertSee('Sauvegarder');
    $response->assertSee('Supprimer');

    Livewire::test(ShowContact::class, ['contact' => $contact])
        ->set('firstname', 'Justin')
        ->assertSet('firstname', 'Justin')
        ->set('lastname', 'Massart')
        ->assertSet('lastname', 'Massart')
        ->set('email', $contact->email)
        ->assertSet('email', $contact->email)
        ->call('updateContact');

    expect($contact->fresh()->firstname)->toBe('Justin')->not->toBe($contact->firstname);
    expect($contact->fresh()->lastname)->toBe('Massart')->not->toBe($contact->lastname);
    expect($contact->fresh()->email)->toBe($contact->email);
});

it('ensures that an authenticated user cannot update one of his own contact with wrong data', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create(['user_id' => $user->id]);

    $response = actingAs($user)->get('/contacts/'.$contact->slug);

    $response->assertOk();
    $response->assertSee($contact->firstname.' '.$contact->lastname);

    $response->assertSee($contact->email);

    $response->assertSee('Sauvegarder');
    $response->assertSee('Supprimer');

    Livewire::test(ShowContact::class, ['contact' => $contact])
        ->set('firstname', 'Justin1')
        ->assertSet('firstname', 'Justin1')
        ->set('lastname', 'Massart2')
        ->assertSet('lastname', 'Massart2')
        ->set('email', 'justinmassart')
        ->assertSet('email', 'justinmassart')
        ->call('updateContact')
        ->assertHasErrors(['email', 'firstname', 'lastname']);
});
