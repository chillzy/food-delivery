<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\Admin;
use Database\Factories\AdminFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminFactory::new()->create();
    }

    public function testAdminSuccessfullyLoggedIn(): void
    {
        $this->postJson($this->getLoginUrl($this->admin->email, AdminFactory::DEFAULT_VALID_PASSWORD))
            ->assertOk()
            ->assertJsonStructure([
                'accessToken',
                'tokenType',
                'expiresIn',
            ]);
    }

    public function testAdminLoginFailed(): void
    {
        $wrongPassword = '124sderte3223';
        $this->postJson($this->getLoginUrl($this->admin->email, $wrongPassword))->assertUnauthorized();

        $wrongEmail = $this->faker->email;
        $this->postJson($this->getLoginUrl($wrongEmail, AdminFactory::DEFAULT_VALID_PASSWORD))
            ->assertUnauthorized();
    }

    public function testAdminSuccessfullyLoggedOut(): void
    {
        $this->loginAs($this->admin, 'admin')
            ->postJson($this->getLogoutUrl())
            ->assertOk();
    }

    public function testLoggedOutAdminCantLogout(): void
    {
        $this->postJson($this->getLogoutUrl())->assertUnauthorized();
    }

    public function testAdminCantLogoutWithUserToken(): void
    {
        $this->loginAs(UserFactory::new()->create(), 'admin')
            ->postJson($this->getLogoutUrl())
            ->assertUnauthorized();
    }

    private function getLoginUrl(string $email, string $password): string
    {
        return URL::route('v1.admin.auth.login', ['email' => $email, 'password' => $password]);
    }

    private function getLogoutUrl(): string
    {
        return URL::route('v1.admin.auth.logout');
    }
}
