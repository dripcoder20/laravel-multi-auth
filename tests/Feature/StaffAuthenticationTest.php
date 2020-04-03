<?php

namespace Tests\Feature;

use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class StaffAuthenticationTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    private $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeader('X-Requested-With', 'XMLHttpRequest');
        $this->withHeader('Accept', 'application/json');

        $this->staff = factory(Staff::class)->create([
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
        $this->post('api/staff/auth/login', ['email' => $this->staff->email, 'password' => '123456'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['token', 'message']);

        $user = auth('staff-api')->user();

        $this->assertEquals($user->id, $this->staff->id);
    }

    /**
     * @test
     */
    public function it_can_open_authenticated_routes_with_the_token()
    {
        $this->withoutExceptionHandling();
        $this->post('api/staff/auth/login', ['email' => $this->staff->email, 'password' => '123456']);
        $this->get('api/staff/me')->assertJsonStructure(['data'])->assertSee($this->staff->name);
        $this->assertAuthenticatedAs($this->staff);
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_when_credentials_are_invalid()
    {
        $this->post('api/staff/auth/login', ['email' => $this->staff->email, 'password' => '1234567'])->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_should_logout_user()
    {
        $this->withoutExceptionHandling();
        $this->post('api/staff/auth/login', ['email' => $this->staff->email, 'password' => '123456']);
        $this->delete('api/staff/auth/logout')->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertGuest('api');
    }

}
