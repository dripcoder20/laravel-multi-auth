<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeader('X-Requested-With', 'XMLHttpRequest');
        $this->withHeader('Accept', 'application/json');

        $this->user = factory(User::class)->create([
            'email' => 'test@example.com',
            'password' => bcrypt("123456"),
        ]);
    }

    /**
     * @test
     * */
    public function it_should_return_jwt_key_from_login()
    {
        $this->withoutExceptionHandling();
        $this->post('api/auth/login', ['email' => $this->user->email, 'password' => '123456'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['token', 'message']);

        $user = auth('api')->user();

        $this->assertEquals($user->id, $this->user->id);
    }

    /**
     * @test
     */
    public function it_can_open_authenticated_routes_with_the_token()
    {
        $this->withoutExceptionHandling();
        $this->post('api/auth/login', ['email' => $this->user->email, 'password' => '123456']);
        $this->get('api/account/me')->assertJsonStructure(['data'])->assertSee($this->user->name);
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_when_credentials_are_invalid()
    {
        $this->post('api/auth/login', ['email' => $this->user->email, 'password' => '1234567'])->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_should_logout_user()
    {
        $this->withoutExceptionHandling();
        $this->post('api/auth/login', ['email' => $this->user->email, 'password' => '123456']);
        $this->delete('api/auth/logout')->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertGuest('api');
    }
}
