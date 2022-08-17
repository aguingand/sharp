<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Contracts\Auth\Authenticatable;

class AuthenticationTest extends BaseApiTest
{
    /** @test */
    public function unauthenticated_user_wont_pass_on_an_api_call()
    {
        $this->withoutExceptionHandling();
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person')->assertStatus(401);
    }

    /** @test */
    public function unauthenticated_user_are_redirected_on_a_web_call()
    {
        $this->buildTheWorld();

        $this->get('/sharp/s-list/person')
            ->assertRedirect('/sharp/login');
    }

    /** @test */
    public function authenticated_user_are_redirected_on_a_guest_route()
    {
        $this->withoutExceptionHandling();
        $this->buildTheWorld();

        $this->login();

        $this->get('/sharp/login')
            ->assertRedirect('/sharp');
    }

    /** @test */
    public function we_can_configure_a_custom_auth_guard()
    {
        $this->buildTheWorld();

        $authGuard = $this->configureCustomAuthGuard();

        $this->login();

        $this->get('/sharp/s-list/person')->assertOk();
        $this->getJson('/sharp/api/list/person')->assertOk();

        $authGuard->setInvalid();

        // We're logged, but not as a sharp user (our fake guard tells us that).
        $this->get('/sharp/s-list/person')->assertRedirect('/sharp/login');
        $this->getJson('/sharp/api/list/person')->assertStatus(401);
    }

    /** @test */
    public function we_can_configure_a_custom_auth_check()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.auth.check_handler',
            AuthenticationTestCheckHandler::class,
        );

        $this->actingAs(new User(['name' => 'ok']));

        $this->get('/sharp/s-list/person')->assertOk();
        $this->json('get', '/sharp/api/list/person')->assertOk();

        $this->actingAs(new User(['name' => 'ko']));

        // We're logged, but not as a sharp user (our fake auth check tells us that).
        $this->get('/sharp/s-list/person')->assertRedirect('/sharp/login');
        $this->json('get', '/sharp/api/list/person')->assertStatus(401);
    }

    /**
     * @return AuthenticationTestGuard
     */
    protected function configureCustomAuthGuard(): AuthenticationTestGuard
    {
        $authGuard = new AuthenticationTestGuard(true);

        auth()->extend('sharp', function () use ($authGuard) {
            return $authGuard;
        });

        $this->app['config']->set(
            'sharp.auth.guard',
            'sharp',
        );

        $this->app['config']->set(
            'auth.guards.sharp', [
                'driver' => 'sharp',
                'provider' => 'users',
            ],
        );

        return $authGuard;
    }
}

class AuthenticationTestGuard implements \Illuminate\Contracts\Auth\Guard
{
    public function __construct(private bool $isValid)
    {
    }

    public function check()
    {
        return $this->isValid;
    }

    public function guest()
    {
        return ! $this->isValid;
    }

    public function user()
    {
        return $this->isValid ? new User() : null;
    }

    public function id()
    {
        return $this->isValid ? 1 : null;
    }

    public function validate(array $credentials = [])
    {
        return true;
    }

    public function setUser(Authenticatable $user)
    {
    }

    public function hasUser()
    {
        return $this->isValid;
    }

    public function setInvalid()
    {
        $this->isValid = false;
    }

    public function authenticate()
    {
    }

    public function logout()
    {
    }
}

class AuthenticationTestCheckHandler
{
    public function check($user)
    {
        return $user->name == 'ok';
    }
}
