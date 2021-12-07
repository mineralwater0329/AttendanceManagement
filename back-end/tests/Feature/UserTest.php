<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
  use DatabaseTransactions, WithFaker;
  private $url;
  // setup
  public function setUp(): void
  {
    parent::setUp();
    $this->user = User::factory()->create();
    $this->url = '/api/user';
  }
  /**
   * A basic feature test example.
   *
   * @return void
   */
  public function test_login_success()
  {
    $this->assertGuest('user');

    $this->url = $this->url.'/login';
    $response = $this->post($this->url, [
      'login_id' => $this->user->login_id,
      'password' => 'password',
    ]);
    $response->assertStatus(200);
    $this->assertAuthenticated('user');
  }

  public function test_login_failed()
  {
    $this->assertGuest('user');

    $this->url = $this->url.'/login';
    
    $response = $this->postJson($this->url, []);
    $response->assertStatus(400);
    $response->assertJsonFragment(['fail val']);

    $response = $this->postJson($this->url, [
      'login_id' => 'fajdlfjaljdlfadddddd',
      'password' => 'password',
    ]);
    $response->assertStatus(400);
    $response->assertJsonFragment(['fail val']);

    $response = $this->postJson($this->url, [
      'login_id' => $this->user->login_id
    ]);
    $response->assertStatus(400);

    $response = $this->postJson($this->url, [
      'password' => 'password'
    ]);
    $response->assertStatus(400);

    $response = $this->postJson($this->url, [
      'login_id' => $this->user->login_id,
      'password' => 'lfajdflkj'
    ]);
    $response->assertStatus(400);
  }

  public function test_logout_success()
  {
    $this->url = $this->url.'/logout';

    $this->assertGuest('user');
    Auth::login($this->user);
    $this->assertAuthenticatedAs($this->user, 'user');
    $response = $this->postJson($this->url);
    $response->assertOk();
    $this->assertGuest('user');
  }

  public function test_logout_failed()
  {
    $this->url = $this->url.'/logout';

    $this->assertGuest('user');
    $response = $this->postJson($this->url);
    $response->assertStatus(401);
    $response->assertJsonFragment(['Unauthenticated']);
  }

  public function test_logout()
  {
    $this->url = $this->url.'/logout';
    
    $this->assertGuest('user');
    $response = $this->actingAs($this->user)->postJson($this->url);
    $response->assertOk();
    $user = Auth::guard('user')->check();
    $admin = Auth::guard('admin')->check();
    info('user', [$user]);
    info('admin', [$admin]);
    $this->assertGuest('user');
  }
}