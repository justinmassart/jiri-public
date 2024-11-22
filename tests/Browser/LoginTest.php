<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseTruncation;

    /**
     * A Dusk test example.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    public function testLoginRoute(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Jiri')
                ->assertSee('Connectez-vous')
                ->assertSee('Créer votre compte');
        });
    }

    public function testLoginForm(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Connectez-vous')
                ->type('.login-form__email input', 'justin@massart.be')
                ->type('.login-form__password input', 'password')
                ->press('Se connecter')
                ->pause(500)
                ->assertPathIs('/dashboard')
                ->assertSee('Dashboard')
                ->assertAuthenticated()
                ->logout();
        });
    }

    public function testLoginFormPasswordError(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Connectez-vous')
                ->type('.login-form__email input', 'justin@massart.be')
                ->type('.login-form__password input', 'wrong-password')
                ->press('Se connecter')
                ->pause(500)
                ->assertPathIs('/')
                ->assertSee('Connectez-vous')
                ->assertSee('Le mot de passe est incorrect.');
        });
    }

    public function testLoginFormEmailError(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Connectez-vous')
                ->type('.login-form__email input', 'wrong@email.com')
                ->type('.login-form__password input', 'password')
                ->press('Se connecter')
                ->pause(500)
                ->assertPathIs('/')
                ->assertSee('Connectez-vous')
                ->assertSee('The selected email is invalid.');
        });
    }

    public function testLogoutForm(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/')
                ->pause(500)
                ->click('div.nav__drawer')
                ->pause(500)
                ->assertSee('Se déconnecter')
                ->press('Se déconnecter')
                ->pause(500)
                ->assertPathIs('/')
                ->assertSee('Connectez-vous')
                ->assertGuest();
        });
    }
}
