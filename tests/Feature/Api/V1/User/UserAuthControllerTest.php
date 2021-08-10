<?php

namespace Tests\Feature\Api\V1\User;

use App\Models\User;
use Database\Factories\AdminFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UserAuthControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
    }

    public function testUserSuccessfullyLoggedIn(): void
    {
        $this->post($this->getLoginUrl($this->user->email, UserFactory::DEFAULT_VALID_PASSWORD))
            ->assertOk()
            ->assertJsonStructure([
                'accessToken',
                'tokenType',
                'expiresIn',
            ]);
    }

    public function testUserLoginFailed(): void
    {
        $wrongPassword = '124sderte3223';
        $this->post($this->getLoginUrl($this->user->email, $wrongPassword))->assertUnauthorized();

        $wrongEmail = $this->faker->email;
        $this->post($this->getLoginUrl($wrongEmail, UserFactory::DEFAULT_VALID_PASSWORD))
            ->assertUnauthorized();

        /** @var User $notVerifiedUser */
        $notVerifiedUser = UserFactory::new()->notVerified()->create();

        $this->post($this->getLoginUrl($notVerifiedUser->email, UserFactory::DEFAULT_VALID_PASSWORD))
            ->assertForbidden();
    }

    public function testUserSuccessfullyLoggedOut(): void
    {
        $this->loginAs($this->user)
            ->post($this->getLogoutUrl())
            ->assertOk();
    }

    public function testLoggedOutUserCantLogout(): void
    {
        $this->post($this->getLogoutUrl())->assertUnauthorized();
    }

    public function testUserCantLogoutWithAdminToken(): void
    {
        $this->loginAs(AdminFactory::new()->create())
            ->post($this->getLogoutUrl())
            ->assertUnauthorized();
    }

    private function getLoginUrl(string $email, string $password): string
    {
        return URL::route('v1.auth.login', ['email' => $email, 'password' => $password]);
    }

    private function getLogoutUrl(): string
    {
        return URL::route('v1.auth.logout');
    }
}
