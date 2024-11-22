<?php

namespace Tests\Feature\Contacts;

use App\Livewire\Contacts\AddContactModal;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('creates a new contact successfully', function () {
    $user = User::factory()->create();

    $contactData = [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'john@doe.com',
    ];

    actingAs($user);

    Livewire::test(AddContactModal::class)
        ->set('firstname', $contactData['firstname'])
        ->set('lastname', $contactData['lastname'])
        ->set('email', $contactData['email'])
        ->call('createContact');

    assertDatabaseHas('contacts', [
        'firstname' => $contactData['firstname'],
        'lastname' => $contactData['lastname'],
        'email' => $contactData['email'],
        'slug' => Str::slug($contactData['firstname'].' '.$contactData['lastname']),
    ]);
});

it('updates an existing contact successfully', function () {
    $user = User::factory()->create();
    $contact = $user->contacts()->create([
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'john@doe.com',
        'slug' => Str::slug('John Doe'),
    ]);

    $updatedContactData = [
        'firstname' => 'Jane',
        'lastname' => 'Doe',
        'email' => 'jane@doe.com',
    ];

    actingAs($user);

    Livewire::test(AddContactModal::class)
        ->set('contactId', $contact->id)
        ->set('firstname', $updatedContactData['firstname'])
        ->set('lastname', $updatedContactData['lastname'])
        ->set('email', $updatedContactData['email'])
        ->call('createContact');

    assertDatabaseHas('contacts', [
        'id' => $contact->id,
        'firstname' => $updatedContactData['firstname'],
        'lastname' => $updatedContactData['lastname'],
        'email' => $updatedContactData['email'],
        'slug' => Str::slug($updatedContactData['firstname'].' '.$updatedContactData['lastname']),
    ]);
});

it('throws an exception when validation fails', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(AddContactModal::class)
        ->set('firstname', '')
        ->set('lastname', '')
        ->set('email', '')
        ->call('createContact')
        ->assertHasErrors(['firstname', 'lastname', 'email']);
});
