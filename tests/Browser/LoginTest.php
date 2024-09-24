<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class LoginTest extends DuskTestCase
{
    /**
     * Test user can log in and log out.
     *
     * @return void
     */
    public function testLoginAndLogout()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('login'))
                    ->assertSee('Login')
                    ->type('email', 'mark@interactivedimension.com')
                    ->type('password', 'changeme')
                    ->press('Login')
                    ->waitForText('Success')
                    ->assertSee('Logged in successfully!')
                    ->assertSeeIn('h1', 'To Do')
                    ->clickLink('Logout')
                    ->assertRouteIs('login');
        });
    }
    
    /**
     * Test that a user can reset their password.
     *
     * @return void
     */
    public function testResetPassword()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('password.request'))
                    ->assertSee('Reset Password')
                    ->type('email', 'mark@interactivedimension.com')
                    ->press('Send Password Reset Link')
                    ->waitForText('Success')
                    ->assertSee('We have emailed you a password reset link.');
        });
        
        $user = User::where('email', 'mark@interactivedimension.com')->first();
        
        // Create a new reset token.
        $token = Password::broker()->createToken($user);
        
        $this->browse(function (Browser $browser) use ($token) {
            $browser->visit(route('password.reset', ['token' => $token]))
                    ->assertSee('Reset Password')
                    ->type('email', 'mark@interactivedimension.com')
                    ->type('password', 'changeme')
                    ->type('password_confirmation', 'changeme')
                    ->press('Reset Password')
                    ->waitForText('Success')
                    ->assertSee('Password reset successfully!');
        });
    }
}
