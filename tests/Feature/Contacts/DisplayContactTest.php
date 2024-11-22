<?php

namespace Tests\Feature\Contacts;

use App\Models\Contact;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('ensures that an authenticated user can see the modal about the contact', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create(['user_id' => $user->id]);

    $response = actingAs($user)->get('/contacts/'.$contact->slug);

    $response->assertOk();
    $response->assertSee($contact->firstname.' '.$contact->lastname);

    $response->assertSee($contact->email);

    $response->assertSee('Sauvegarder');
    $response->assertSee('Supprimer');
});

it('ensures that an authenticated user cannot acces the show modal of another user’s contact', function () {
    $justin = User::factory()->create();
    $john = User::factory()->create();

    $john_contact = Contact::factory()->create(['user_id' => $john->id]);

    $response = actingAs($justin)->get('/contacts/'.$john_contact->slug);

    $response->assertDontSee($john_contact->firstname.' '.$john_contact->lastname);
});
