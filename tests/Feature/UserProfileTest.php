<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\User;

class UserProfileTest extends TestCase
{
    use WithFaker;
    
    /**
     * The logged in user can change their password.
     * @return void
     */
    public function testChangePassword()
    {
        $password = Str::random(12);
        $newPassword = Str::random(16);
        
        $user = factory(User::class)->states('admin')->create(['password' => Hash::make($password)]);
        
        $this->actingAs($user)->get(route('user-profile.edit-password'))->assertStatus(200);
        
        $attributes = [
            'current_password' => $password,
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword
        ];
        
        $response = $this->actingAs($user)->call('PUT', route('user-profile.update-password'), $attributes);
        
        $this->get($response->headers->get('Location'))->assertSee('Password changed successfully!');
    }
    
    /**
     * The logged in user can change their details.
     * @return void
     */
    /*public function testChangeDetails()
    {
        $this->markTestSkipped('This feature has now been removed.');
        
        $user = factory(User::class)->states('admin')->create();
        
        $this->actingAs($user)->get(route('user-profile.edit'))->assertStatus(200);
        
        $allowedDomains = User::getAllowedDomains();
        $randomKey = array_rand($allowedDomains);
        
        $uniqueSuffix = $this->faker->unique()->word;
        $domain = $allowedDomains[$randomKey];
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->unique()->lastName;
        $email = $firstName.'.'.$lastName.'.'.$uniqueSuffix.'@'.$domain;
        
        $attributes = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email
        ];
        
        $response = $this->actingAs($user)->call('PUT', route('user-profile.update'), $attributes);
        
        $this->get($response->headers->get('Location'))->assertSee('Details changed successfully!');
        
        $attributes['acronym'] = User::updateAcronym($user->id, $firstName, $lastName);
        
        $this->assertDatabaseHas('users', $attributes);
    }*/
}