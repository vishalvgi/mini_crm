<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Company;

class BasicTest extends TestCase {

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample() {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testUserCanSeeLogin() {
        $response = $this->get('/login');

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    public function testUserCannotSeeAfterLogin() {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/home');
    }

    public function testLoginWithValidCredential() {
// commented code to create user directly from here
//        $user = factory(User::class)->create([
//            'password' => bcrypt($password = 'i-love-laravel'),
//        ]);
        $user = User::where('email', 'admin@admin.com')->get()[0];
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    public function testLoginWithInvalidCredential() {
        $user = User::where('email', 'admin@admin.com')->get()[0];
        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function testCreateCompany() {
        $company = [
            'name' => 'Joe Fransisco',
        ];

        // create session first
        $user = User::where('email', 'admin@admin.com')->get()[0];
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response = $this->post('/home', $company);

        $response->assertRedirect('/home')
                ->assertSessionHas('status', __('message.company_saved'));

        $this->assertDatabaseHas('companies', $company);
    }

    public function testCreateExistingCompany() {
        $company = [
            'name' => 'Joe Fransisco',
        ];

        // create session first
        $user = User::where('email', 'admin@admin.com')->get()[0];
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response = $this->post('/home', $company);

        $response->assertRedirect('/home/create')
                ->assertSessionHasErrors('name');
    }

}
