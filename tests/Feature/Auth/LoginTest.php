<?php

use App\Livewire\LoginForm;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('ensures that the home route returns a successful response', function () {
    $response = get('/');

    $response->assertOk();
});

it('ensures that a non-authenticated user see the login-or-register view with the login-form when visiting the app', function () {
    $response = get('/');

    $response->assertOk();
    $response->assertSeeLivewire('login-form');
});

it('ensures that a authenticated user see the home page when visiting the app', function () {
    $response = actingAs(User::factory()->create())->get('/dashboard');

    $response->assertOk();

    $response->assertDontSeeLivewire('login-form');
    $response->assertDontSeeLivewire('register-form');

    $response->assertSee('Se déconnecter');
});

it('ensures that a non-authenticated user can log itself in the login-form on the home page', function () {
    // not usefull
    $user = User::factory()->create();
    $response = get('/');

    $response
        ->assertOk()
        ->assertSeeLivewire('login-form');

    // can only test the login form by setting ourself user data ?
    Livewire::test(LoginForm::class)
        ->set('email', $user->email)
        ->assertSet('email', $user->email)
        ->set('password', 'password')
        ->assertSet('password', 'password')
        ->call('login');

    expect(auth()->user()->email)->toBe($user->email);
});

it('ensures that a non-authenticated user cannot log itslef with wrong email/password', function () {
    // not usefull
    $user = User::factory()->create();
    $response = get('/');

    $response->assertOk();
    $response->assertSeeLivewire('login-form');

    // can only test the login form by setting ourself user data ?
    // testing for email errors
    $wrong_email = 'test@test.com';
    expect($wrong_email)->not->toBe($user->email);

    Livewire::test(LoginForm::class)
        ->set('email', $wrong_email)
        ->assertSet('email', $wrong_email)
        ->set('password', 'password')
        ->assertSet('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);

    // testing for password errors
    $wrong_password = 'wrong_password';
    expect($wrong_password)->not->toBe($user->password);
    $check_hash = Hash::check('wrong_password', $user->password);
    expect($check_hash)->toBeFalse();

    Livewire::test(LoginForm::class)
        ->set('email', $user->email)
        ->assertSet('email', $user->email)
        ->set('password', $wrong_password)
        ->assertSet('password', $wrong_password)
        ->call('login')
        ->assertHasErrors(['password']);
});
