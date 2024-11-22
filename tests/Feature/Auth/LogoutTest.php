<?php

namespace Tests\Feature\Auth;

use App\Livewire\Navigation;
use App\Models\User;
use Livewire\Livewire;

it('ensures that an authenticated user can log itself out', function () {
    $user = User::factory()->create();

    auth()->login($user);

    Livewire::test(Navigation::class)
        ->call('logout', $user->id);

    expect(auth()->check())->toBeFalse();
});

it('ensures that an authenticated user cannot log another user out', function () {
    $user = User::factory()->create();
    $anotherUser = User::factory()->create();

    auth()->login($anotherUser);
    auth()->login($user);

    expect(auth()->check($user))->toBeTrue();
    expect(auth()->check($anotherUser))->toBeTrue();

    Livewire::test(Navigation::class)
        ->call('logout', $anotherUser->id)
        ->assertForbidden();

    expect(auth()->check($anotherUser))->toBeTrue();
    expect(auth()->check($user))->toBeTrue();
});

it('ensures that an authenticated user is redirected to the LoginOrRegister view after being logged out', function () {
    $user = User::factory()->create();

    auth()->login($user);

    Livewire::test(Navigation::class)
        ->call('logout', $user->id)
        ->assertRedirect(route('login'));
});
