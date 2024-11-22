<?php

namespace Tests\Feature\Contacts;

use App\Models\Contact;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('ensures that an authenticated user can visit the contacts page', function () {
    $response = actingAs(User::factory()->create())->get('/contacts');

    $response->assertOk();
});

it('ensures that a non authenticated user cannot visit the contacts page', function () {
    $response = get('/contacts');

    $response->assertRedirect('/');
});

it('ensures that an authenticated user can see his own 12 first contacts paginated', function () {
    $user = User::factory()->create();
    $contacts = Contact::factory(50)->create(['user_id' => $user->id]);

    $response = actingAs($user)->get('/contacts');

    $response->assertOk();
    $response->assertSee('Contacts');
    $response->assertSeeInOrder($contacts->sortBy('lastname')->pluck(['lastname', 'firstname', 'email'])->take(12)->toArray());
});

it('ensures that an authenticated user can see the pagination in the contacts page', function () {
    $user = User::factory()->create();
    $contacts = Contact::factory(53)->create(['user_id' => $user->id]);
    $pagesNumber = intval(ceil($contacts->count() / 12));

    $response = actingAs($user)->get('/contacts');

    $response->assertOk();
    $response->assertSee('Contacts');
    $response->assertSeeInOrder($contacts->sortBy('lastname')->pluck(['lastname', 'firstname', 'email'])->take(12)->toArray());

    for ($i = 1; $i <= $pagesNumber; $i++) {
        $response->assertSee('page = '.$i);
    }
});

it('ensures that an authenticated user can go through the pagination to see all of his contacts', function () {
    $user = User::factory()->create();
    $contacts = Contact::factory(45)->create(['user_id' => $user->id]);
    $pagesNumber = intval(ceil($contacts->count() / 12));

    $response = actingAs($user)->get('/contacts');

    $response->assertOk();
    $response->assertSee('Contacts');
    $response->assertSeeInOrder($contacts->sortBy('lastname')->pluck(['lastname', 'firstname', 'email'])->take(12)->toArray());

    for ($i = 1; $i <= $pagesNumber; $i++) {
        $response->assertSee('page = '.$i);
        $response = actingAs($user)->get('/contacts?page='.$i);
        $response->assertSeeInOrder($contacts->sortBy('lastname')->pluck(['lastname', 'firstname', 'email'])->skip(($i - 1) * 12)->take(12)->toArray());
    }
});

it('ensures that an authenticated user can search for contacts in the contacts page', function () {
    $user = User::factory()->create();
    $contacts = Contact::factory(10)->create(['user_id' => $user->id]);
    $john = Contact::factory()->create(['user_id' => $user->id, 'firstname' => 'John', 'lastname' => 'Doe', 'email' => 'john@doe.com']);
    $justin = Contact::factory()->create(['user_id' => $user->id, 'firstname' => 'Justin', 'lastname' => 'Massart', 'email' => 'justin@massart.com']);

    $response = actingAs($user)->get('/contacts');

    $response->assertOk();
    $response->assertSee('Contacts');

    $response = actingAs($user)->get('/contacts?search=John+Doe');

    $response->assertOk();
    $response->assertSee('Contacts');
    $response->assertSee($john->lastname);
    $response->assertDontSee($justin->lastname);
});

it('ensures that an authenticated user can see his contacts in the basic order', function () {
    $user = User::factory()->create();
    auth()->login($user);

    $contacts = Contact::factory(10)->create(['user_id' => $user->id]);

    $base_ordered_contacts = Contact::whereUserId(auth()->user()->id)
        ->orderBy('lastname', 'asc')
        ->get()
        ->map(function ($contact) {
            return $contact->firstname;
        })
        ->toArray();

    $response = actingAs($user)->get('/contacts');

    $response->assertOk();
    $response->assertSee('Contacts');

    $response->assertSeeInOrder($base_ordered_contacts);
});

it('ensures that an authenticated user can sort his contacts', function () {
    $user = User::factory()->create();
    auth()->login($user);

    $contacts = Contact::factory(10)->create(['user_id' => $user->id]);

    $response = actingAs($user)->get('/contacts?sort=firstname&order=desc');

    $response->assertOk();
    $response->assertSee('Contacts');

    $ordered_contacts = Contact::whereUserId(auth()->user()->id)
        ->orderBy('firstname', 'desc')
        ->get()
        ->map(function ($contact) {
            return $contact->firstname;
        })
        ->toArray();

    $response->assertSeeInOrder($ordered_contacts);
});
