<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    use DatabaseTruncation;

    /**
     * A Dusk test example.
     */
    public function testRegisterRoute(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Jiri')
                ->assertSee('Créer votre compte')
                ->assertSee('Créer mon compte');
        });
    }

    public function testRegisterForm(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Créer votre compte')
                ->type('.register-form__firstname input', 'John')
                ->type('.register-form__lastname input', 'Doe')
                ->type('.register-form__email input', 'john@doe.com')
                ->type('.register-form__password input', 'password')
                ->type('.register-form__confirm-password input', 'password')
                ->press('Créer mon compte')
                ->pause(500)
                ->assertPathIs('/dashboard')
                ->assertSee('Dashboard')
                ->assertAuthenticated()
                ->logout();
        });
    }

    public function testRegisterFormErrors(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Créer votre compte')
                ->type('.register-form__firstname input', '123')
                ->type('.register-form__lastname input', '&é"')
                ->type('.register-form__email input', 'john@doe.com')
                ->type('.register-form__password input', 'sbeb')
                ->type('.register-form__confirm-password input', 'sbjhfbsdjhfk')
                ->press('Créer mon compte')
                ->pause(500)
                ->assertSee('The firstname field format is invalid.')
                ->assertSee('The lastname field format is invalid.')
                ->assertSee('The password field must be at least 8 characters.')
                ->assertSee('The confirm password field must match password.');
        });
    }
}
