<?php

namespace Tests\Feature\Api\V1\User;

use App\Models\EmailVerification;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function testUserSuccessfullyCreated(): void
    {
        Notification::fake();

        $name = $this->faker->name();
        $email = $this->faker->email;

        $requestData = [
            'name' => $name,
            'email' => $email,
            'password' => '12Gfo%aawR',
        ];

        $response = $this->post($this->getCreateUrl(), $requestData);

        $response->assertCreated()
            ->assertJson([
                'name' => $name,
                'email' => $email,
            ]);

        $this->assertDatabaseHas(User::class, [
            'name' => $name,
            'email' => $email,
        ]);

        $createdUser = User::find($response['id']);
        $this->assertDatabaseHas(EmailVerification::class, [
            'user_id' => $createdUser->id,
        ]);

        Notification::assertSentTo($createdUser, VerifyEmail::class);
    }

    public function testUserCreationFailedWithInvalidData(): void
    {
        Notification::fake();

        $validName = $this->faker->name();
        $validEmail = $this->faker->email;
        $validPassword = '12Gfo%aawR';

        $invalidName = $this->faker->realTextBetween(51, 100);

        $route = $this->getCreateUrl();

        $this->post($route, [
            'name' => $invalidName,
            'email' => $validEmail,
            'password' => $validPassword,
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseMissing(User::class, [
            'name' => $invalidName,
            'email' => $validEmail,
        ]);

        Notification::assertNothingSent();

        $invalidEmail = $this->faker->word();
        $this->post($route, [
            'name' => $validName,
            'email' => $invalidEmail,
            'password' => $validPassword,
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseMissing(User::class, [
            'name' => $validName,
            'email' => $invalidEmail,
        ]);

        Notification::assertNothingSent();

        $invalidPassword = '123';
        $this->post($route, [
            'name' => $validName,
            'email' => $validEmail,
            'password' => $invalidPassword,
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseMissing(User::class, [
            'name' => $validName,
            'email' => $validEmail,
        ]);

        Notification::assertNothingSent();
    }

    public function testUserSuccessfullyVerified(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->notVerified()->create();

        $this->get($this->getVerifyUrl($user->emailVerification->verification_token))->assertOk();

        $verifiedUser = User::find($user->id);
        $this->assertTrue($verifiedUser->hasVerifiedEmail());
    }

    public function testUserVerificationFailed(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->notVerified()->create();

        $route = $this->getVerifyUrl($this->faker->uuid);
        $response = $this->get($route);
        $response->assertNotFound();

        $notVerifiedUser = User::find($user->id);
        $this->assertFalse($notVerifiedUser->hasVerifiedEmail());
    }

    private function getCreateUrl(): string
    {
        return URL::route('v1.user.create');
    }

    private function getVerifyUrl(string $verificationToken): string
    {
        return URL::route('v1.user.verify', ['verificationToken' => $verificationToken]);
    }
}
