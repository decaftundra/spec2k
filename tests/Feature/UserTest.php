<?php

namespace Tests\Feature;

use App\Role;
use App\User;
use App\Location;
use App\Message;
use Tests\TestCase;
use App\Codes\Airline;
use Illuminate\Support\Str;
use App\Mail\UserRegistered;
use App\Mail\UserPasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use WithFaker;
    
    /**
     * Normal users cannot access the admin pages.
     * @return void
     */
    public function testUsersCannotAccessAdmin()
    {
        $this->actingAs($this->user)
            ->get(route('user.index'))
            ->assertStatus(403);
        
        $response = $this->actingAs($this->user)
            ->get(route('user.create'))
            ->assertStatus(403);
        
        $response = $this->actingAs($this->user)
            ->get(route('user.edit', User::inRandomOrder()->first()->id))
            ->assertStatus(403);
    }
    
    /**
     * Admin users can access the admin page.
     * @return void
     */
    public function testAdminsCanAccessAdmin()
    {
        $response = $this->actingAs($this->adminUser)->get(route('user.index'))->assertStatus(200);
        
        $response = $this->actingAs($this->siteAdminUser)->get(route('user.index'))->assertStatus(200);
        
        $response = $this->actingAs($this->dataAdminUser)->get(route('user.index'))->assertStatus(200);
    }
    
    /**
     * Test site admin can create a user for the same site.
     * @return void
     */
    public function testSiteAdminCanCreateUserForSameSite()
    {
        Mail::fake();
        
        $this->actingAs($this->siteAdminUser)->get(route('user.create'))->assertStatus(200);
        
        $newUser = factory(User::class)->states('user')->make(['location_id' => $this->siteAdminUser->location_id]);
        $userAttributes = $newUser->toArray();
        unset($userAttributes['fullname']);
        unset($userAttributes['remember_token']);
        unset($userAttributes['password']);
        
        $response = $this->actingAs($this->siteAdminUser)->call('POST', route('user.store'), $userAttributes);
        
        $this->followRedirects($response)->assertSee('New user registered successfully!');
        
        $this->assertDatabaseHas('users', $userAttributes);
        
        Mail::assertSent(UserRegistered::class);
    }
    
    /**
     * Test site admin can create an admin for the same site.
     * @return void
     */
    public function testSiteAdminCanCreateAdminForSameSite()
    {
        Mail::fake();
        
        $this->actingAs($this->siteAdminUser)->get(route('user.create'))->assertStatus(200);
        
        $newUser = factory(User::class)->states('admin')->make(['location_id' => $this->siteAdminUser->location_id]);
        $adminAttributes = $newUser->toArray();
        unset($adminAttributes['fullname']);
        unset($adminAttributes['remember_token']);
        unset($adminAttributes['password']);
        
        $response = $this->actingAs($this->siteAdminUser)->call('POST', route('user.store'), $adminAttributes);
            
        $this->get($response->headers->get('Location'))->assertSee('New user registered successfully!');
        
        $this->assertDatabaseHas('users', $adminAttributes);
        
        Mail::assertSent(UserRegistered::class);
    }
    
    /**
     * Test a site admin can't upgrade a user to data admin.
     * @return void
     */
    public function testSiteAdminCantUpgradeAUserToDataAdmin()
    {
        $user = User::where('location_id', $this->siteAdminUser->location_id)
            ->where('role_id', '<=', $this->siteAdminUser->role_id)
            ->where('id', '!=', $this->siteAdminUser->id)
            ->firstOrFail();
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('user.edit', $user->id))
            ->assertStatus(200);
        
        $attributes = $user->toArray();
        $attributes['role_id'] = 4;
        unset($attributes['fullname']);
        unset($attributes['remember_token']);
        unset($attributes['password']);
        
        $response = $this->actingAs($this->siteAdminUser)
            ->call('PUT', route('user.update', $user->id), $attributes)
            ->assertStatus(403);
    }
    
    /**
     * Test a site admin can't make changes to another site's user.
     * @return void
     */
    public function testSiteAdminCantUpdateAUserFromADifferentSite()
    {
        $user = User::where('location_id', '!=', $this->siteAdminUser->location_id)
            ->where('role_id', '<=', $this->siteAdminUser->role_id)
            ->where('id', '!=', $this->siteAdminUser->id)
            ->firstOrFail();
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('user.edit', $user->id))
            ->assertStatus(403);
        
        $attributes = $user->toArray();
        $attributes['location_id'] = $this->siteAdminUser->location_id;
        unset($attributes['fullname']);
        unset($attributes['remember_token']);
        unset($attributes['password']);
        
        $response = $this->actingAs($this->siteAdminUser)
            ->call('PUT', route('user.update', $user->id), $attributes)
            ->assertStatus(403);
    }
    
    /**
     * Test data admin can create a site admin for a different site.
     * @return void
     */
    public function testDataAdminCanCreateASiteAdminForDifferentSite()
    {
        Mail::fake();
        
        $this->actingAs($this->dataAdminUser)->get(route('user.create'))->assertStatus(200);
        
        $dataAdminlocationId = $this->dataAdminUser->location_id;
        
        $locationId = $dataAdminlocationId > 1 ? $dataAdminlocationId-- : $dataAdminlocationId++;
        
        $newUser = factory(User::class)->states('site_admin')->make(['location_id' => $locationId]);
        $adminAttributes = $newUser->toArray();
        unset($adminAttributes['fullname']);
        unset($adminAttributes['remember_token']);
        unset($adminAttributes['password']);
        unset($adminAttributes['acronym']);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('POST', route('user.store'), $adminAttributes);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('New user registered successfully!');
        
        $this->assertDatabaseHas('users', $adminAttributes);
        
        Mail::assertSent(UserRegistered::class);
    }
    
    /**
     * Test password reset and login.
     *
     * @return void
     */
    public function testUserPasswordResetAndLogin()
    {
        Notification::fake();
        
        // Get the reset form page.
        $this->get(route('password.request'))
            ->assertStatus(200)
            ->assertSee('Reset Password');
            
        $inactiveRoleId = Role::where('name', 'inactive')->first()->id;
            
        // Get a random active user.
        $randomUser = User::where('role_id', '!=', $inactiveRoleId)->inRandomOrder()->first();
        
        // Request password reset.
        $response = $this->call('POST', route('password.email'), ['email' => $randomUser->email])
            ->assertStatus(302);
            
        $this->followRedirects($response)
            ->assertSee('We have emailed you a password reset link.');
        
        $token = '';
        
        // Check the reset notification has been sent and get reset token.
        Notification::assertSentTo($randomUser, ResetPasswordNotification::class, function($notification, $channels) use (&$token) {
            $token = $notification->token;

            return true;
        });
        
        // Get the reset password form.
        $this->get(route('password.reset', ['token' => $token]))
            ->assertStatus(200)
            ->assertSee('Reset Password')
            ->assertSee('Confirm Password');
            
        $password = Str::random(12);
            
        $attributes = [
            'token' => $token,
            'email' => $randomUser->email,
            'password' => $password,
            'password_confirmation' => $password
        ];
        
        // Reset user's password, this will also log user in if successful.
        $response = $this->call('POST', route('password.postReset'), $attributes)
            ->assertStatus(302);
        
        $this->followRedirects($response)
            ->assertSee('Password reset successfully!');
            
        // Log user out.
        $this->post(route('logout'))->assertStatus(302);
        
        $this->assertGuest();
            
        // Test user can login.
        $this->get(route('login'))
            ->assertStatus(200)
            ->assertSee('Login')
            ->assertSee('Remember Me')
            ->assertSee('Forgot Your Password?');
            
        $attributes = [
            'email' => $randomUser->email,
            'password' => $password
        ];
        
        $this->assertCredentials($attributes);
            
        $response = $this->call('POST', route('postLogin'), $attributes)
            ->assertStatus(302);
        
        $this->followRedirects($response)
            ->assertSee('Logged in successfully!')
            ->assertSee('To Do');
            
        $this->assertAuthenticatedAs($randomUser);
    }
    
    /**
     * Test that a bad password shows the expected error message.
     *
     * @return void
     */
    public function testIncorrectPassword()
    {
        $inactiveRoleId = Role::where('name', 'inactive')->first()->id;
        
        $attributes = [
            'email' => User::where('role_id', '!=', $inactiveRoleId)->inRandomOrder()->first()->email,
            'password' => Str::random(12)
        ];
        
        $this->assertInvalidCredentials($attributes);
        
        $response = $this->call('POST', route('postLogin'), $attributes)
            ->assertSessionHasErrors(['email' => 'These credentials do not match our records.'])
            ->assertStatus(302);
    }
    
    /**
     * Test a data admin can update a user for any site.
     * @return void
     */
    public function testDataAdminCanUpdateUserForDifferentSite()
    {
        $dataAdminlocationId = $this->dataAdminUser->location_id;
        
        $locationId = $dataAdminlocationId > 1 ? $dataAdminlocationId-- : $dataAdminlocationId++;
        
        $user = User::where('location_id', $locationId)->where('role_id', 1)->firstOrFail();
        
        $this->actingAs($this->dataAdminUser)->get(route('user.edit', $user->id))->assertStatus(200);
        
        $attributes = $user->toArray();
        
        $attributes['first_name'] = $this->faker->firstName;
        $attributes['last_name'] = $this->faker->lastName;
        
        unset($attributes['fullname']);
        unset($attributes['remember_token']);
        unset($attributes['password']);
        unset($attributes['created_at']);
        unset($attributes['updated_at']);
        unset($attributes['acronym']);
        
        $response = $this->actingAs($this->dataAdminUser)->call('PUT', route('user.update', $user->id), $attributes);
            
        $this->get($response->headers->get('Location'))->assertSee('User updated successfully!');
        
        $this->assertDatabaseHas('users', $attributes);
    }
    
    /**
     * Test a data admin can delete a user for any site.
     * @return void
     */
    public function testDataAdminCanDeleteUserForDifferentSite()
    {
        $this->markTestSkipped('Deleting user is currently disabled.');
        
        $dataAdminlocationId = $this->dataAdminUser->location_id;
        
        $locationId = $dataAdminlocationId > 1 ? $dataAdminlocationId-- : $dataAdminlocationId++;
        
        $user = User::where('location_id', $locationId)->where('role_id', 1)->firstOrFail();
        
        $this->actingAs($this->dataAdminUser)->get(route('user.delete', $user->id))->assertStatus(200);
        
        $response = $this->actingAs($this->dataAdminUser)->call('DELETE', route('user.destroy', $user->id));
            
        $this->get($response->headers->get('Location'))->assertSee('User deleted successfully!');
        
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
    
    /**
     * Test a site admin can delete a user from the same site.
     * @return void
     */
    public function testSiteAdminCanDeleteUserForSameSite()
    {
        $this->markTestSkipped('Deleting user is currently disabled.');
        
        $user = User::where('location_id', $this->siteAdminUser->location_id)
            ->where('role_id', '<=', $this->siteAdminUser->role_id)
            ->where('id', '!=', $this->siteAdminUser->id)
            ->firstOrFail();
        
        $this->actingAs($this->siteAdminUser)->get(route('user.delete', $user->id))->assertStatus(200);
        
        $response = $this->actingAs($this->siteAdminUser)->call('DELETE', route('user.destroy', $user->id));
            
        $this->get($response->headers->get('Location'))->assertSee('User deleted successfully!');
        
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
    
    /**
     * Test a site admin can't delete a user from a different site.
     * @return void
     */
    public function testSiteAdminCantDeleteUserFromDifferentSite()
    {
        $this->markTestSkipped('Deleting user is currently disabled.');
        
        $user = User::where('location_id', '!=', $this->siteAdminUser->location_id)
            ->where('role_id', '<=', $this->siteAdminUser->role_id)
            ->firstOrFail();
        
        $this->actingAs($this->siteAdminUser)->get(route('user.delete', $user->id))->assertStatus(403);
        
        $this->actingAs($this->siteAdminUser)->call('DELETE', route('user.destroy', $user->id))->assertStatus(403);
    }
    
    /**
     * Test that a user with saved message preferences can be deleted.
     *
     * @return void
     */
    public function testUserWithMessagesCanBeDeleted()
    {
        $this->markTestSkipped('Deleting user is currently disabled.');
        
        $user = User::where('id', '!=', $this->siteAdminUser->id)->firstOrFail();
        
        $messages = Message::get();
        
        $user->messages()->sync($messages->pluck('id')->toArray());
        
        $this->actingAs($this->siteAdminUser)->get(route('user.delete', $user->id))->assertStatus(200);
        
        $response = $this->actingAs($this->siteAdminUser)->call('DELETE', route('user.destroy', $user->id));
            
        $this->get($response->headers->get('Location'))->assertSee('User deleted successfully!');
        
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
    
    /**
     * Test data admin can make any user inactive.
     *
     * @return void
     */
    public function testDataAdminCanMakeAnyUserInactive()
    {
        $inactiveRoleId = Role::where('name', 'inactive')->first()->id;
        
        $dataAdminlocationId = $this->dataAdminUser->location_id;
        
        $locationId = $dataAdminlocationId > 1 ? $dataAdminlocationId-- : $dataAdminlocationId++;
        
        $user = User::where('location_id', $locationId)->firstOrFail();
        
        $this->actingAs($this->dataAdminUser)->get(route('user.edit', $user->id))->assertStatus(200);
        
        $attributes = $user->toArray();
        
        $attributes['first_name'] = $this->faker->firstName;
        $attributes['last_name'] = $this->faker->lastName;
        $attributes['role_id'] = $inactiveRoleId;
        
        unset($attributes['fullname']);
        unset($attributes['remember_token']);
        unset($attributes['password']);
        unset($attributes['created_at']);
        unset($attributes['updated_at']);
        unset($attributes['acronym']);
        
        $response = $this->actingAs($this->dataAdminUser)->call('PUT', route('user.update', $user->id), $attributes);
            
        $this->get($response->headers->get('Location'))->assertSee('User updated successfully!');
        
        $this->assertDatabaseHas('users', $attributes);
    }
    
    /**
     * Test site admin can make a user inactive from the same site.
     *
     * @return void
     */
    public function testSiteAdminCanMakeUserFromSameSiteInactive()
    {
        $inactiveRoleId = Role::where('name', 'inactive')->first()->id;
        
        $siteAdminlocationId = $this->siteAdminUser->location_id;
        
        $user = User::where('location_id', $siteAdminlocationId)->firstOrFail();
        
        $this->actingAs($this->siteAdminUser)->get(route('user.edit', $user->id))->assertStatus(200);
        
        $attributes = $user->toArray();
        
        $attributes['first_name'] = $this->faker->firstName;
        $attributes['last_name'] = $this->faker->lastName;
        $attributes['role_id'] = $inactiveRoleId;
        
        unset($attributes['fullname']);
        unset($attributes['remember_token']);
        unset($attributes['password']);
        unset($attributes['created_at']);
        unset($attributes['updated_at']);
        unset($attributes['acronym']);
        
        $response = $this->actingAs($this->siteAdminUser)->call('PUT', route('user.update', $user->id), $attributes);
            
        $this->get($response->headers->get('Location'))->assertSee('User updated successfully!');
        
        $this->assertDatabaseHas('users', $attributes);
    }
    
    /**
     * Test site admin can't make a user inactive from a different site.
     *
     * @return void
     */
    public function testSiteAdminCantMakeUserFromDifferentSiteInactive()
    {
        $inactiveRoleId = Role::where('name', 'inactive')->first()->id;
        
        $siteAdminlocationId = $this->siteAdminUser->location_id;
        
        $user = User::where('location_id', '!=', $siteAdminlocationId)->firstOrFail();
        
        $this->actingAs($this->siteAdminUser)->get(route('user.edit', $user->id))->assertStatus(403);
        
        $attributes = $user->toArray();
        
        $attributes['first_name'] = $this->faker->firstName;
        $attributes['last_name'] = $this->faker->lastName;
        $attributes['role_id'] = $inactiveRoleId;
        
        unset($attributes['fullname']);
        unset($attributes['remember_token']);
        unset($attributes['password']);
        unset($attributes['created_at']);
        unset($attributes['updated_at']);
        unset($attributes['acronym']);
        
        $this->actingAs($this->siteAdminUser)->call('PUT', route('user.update', $user->id), $attributes)->assertStatus(403);
    }
    
    /**
     * Test admin user can't make a user from the same site inactive.
     *
     * @return void
     */
    public function testAdminUserCantMakeAUserInactive()
    {
        $inactiveRoleId = Role::where('name', 'inactive')->first()->id;
        
        $adminlocationId = $this->adminUser->location_id;
        
        $user = User::where('location_id', $adminlocationId)->firstOrFail();
        
        $this->actingAs($this->adminUser)->get(route('user.edit', $user->id))->assertStatus(403);
        
        $attributes = $user->toArray();
        
        $attributes['first_name'] = $this->faker->firstName;
        $attributes['last_name'] = $this->faker->lastName;
        $attributes['role_id'] = $inactiveRoleId;
        
        unset($attributes['fullname']);
        unset($attributes['remember_token']);
        unset($attributes['password']);
        unset($attributes['created_at']);
        unset($attributes['updated_at']);
        unset($attributes['acronym']);
        
        $this->actingAs($this->adminUser)->call('PUT', route('user.update', $user->id), $attributes)->assertStatus(403);
    }
    
    /**
     * Test user can't make a user from the same site inactive.
     *
     * @return void
     */
    public function testUserCantMakeAUserInactive()
    {
        $inactiveRoleId = Role::where('name', 'inactive')->first()->id;
        
        $userlocationId = $this->user->location_id;
        
        $user = User::where('location_id', $userlocationId)->where('id', '!=', $this->user->id)->firstOrFail();
        
        $this->actingAs($this->user)->get(route('user.edit', $user->id))->assertStatus(403);
        
        $attributes = $user->toArray();
        
        $attributes['first_name'] = $this->faker->firstName;
        $attributes['last_name'] = $this->faker->lastName;
        $attributes['role_id'] = $inactiveRoleId;
        
        unset($attributes['fullname']);
        unset($attributes['remember_token']);
        unset($attributes['password']);
        unset($attributes['created_at']);
        unset($attributes['updated_at']);
        unset($attributes['acronym']);
        
        $this->actingAs($this->user)->call('PUT', route('user.update', $user->id), $attributes)->assertStatus(403);
    }
    
    /**
     * Test that a site admin can upgrade an inactive user from the same site to a user.
     *
     * @return void
     */
    public function testASiteAdminCanUpgradeAnInactiveUserOfTheSameSite()
    {
        $userRoleId = Role::where('name', 'user')->first()->id;
        
        $userlocationId = $this->siteAdminUser->location_id;
        
        $user = User::inactives()->where('location_id', $userlocationId)->firstOrFail();
        
        $this->actingAs($this->siteAdminUser)->get(route('user.edit', $user->id))->assertStatus(200);
        
        $attributes = $user->toArray();
        
        $attributes['first_name'] = $this->faker->firstName;
        $attributes['last_name'] = $this->faker->lastName;
        $attributes['role_id'] = $userRoleId;
        
        unset($attributes['fullname']);
        unset($attributes['remember_token']);
        unset($attributes['password']);
        unset($attributes['created_at']);
        unset($attributes['updated_at']);
        unset($attributes['acronym']);
        
        $response = $this->actingAs($this->siteAdminUser)
            ->call('PUT', route('user.update', $user->id), $attributes)
            ->assertStatus(302);
        
        $this->get($response->headers->get('Location'))->assertSee('User updated successfully!');
        
        $this->assertDatabaseHas('users', $attributes);
    }
    
    /**
     * Test that an inactive user cannot access the site.
     *
     * @return void
     */
    public function testInactiveUserCantAccessSite()
    {
        $this->actingAs($this->inactiveUser)->call('GET', route('notifications.index'))->assertStatus(403);
        
        $this->actingAs($this->inactiveUser)->call('GET', route('datasets.index'))->assertStatus(403);
    }
}