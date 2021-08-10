<?php

namespace Tests\Feature\Api\V1\User\UserAddressController;

use App\Models\User;
use App\Models\UserAddress;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UserAddressDeleteTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private User $user;

    private UserAddress $userAddress;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
        $this->userAddress = $this->user->addresses->first();
    }

    public function testUserAddressSuccessfullyDeleted(): void
    {
        $this->loginAs($this->user)
            ->deleteJson($this->getUrl($this->userAddress->id))
            ->assertNoContent();

        $this->assertSoftDeleted(UserAddress::class, [
            'id' => $this->userAddress->id,
        ]);

        $this->loginAs($this->user)
            ->deleteJson($this->getUrl($this->userAddress->id))
            ->assertNotFound();
    }

    public function testUserAddressDeletingFailed(): void
    {
        $this->deleteJson($this->getUrl($this->userAddress->id))->assertUnauthorized();

        /** @var User $anotherUser */
        $anotherUser = UserFactory::new()->create();

        /** @var UserAddress $anotherUserAddress */
        $anotherUserAddress = $anotherUser->addresses->first();

        $this->loginAs($this->user)
            ->deleteJson($this->getUrl($anotherUserAddress->id))
            ->assertForbidden();

        $this->assertDatabaseHas(UserAddress::class, [
            'id' => $anotherUserAddress->id,
        ]);
    }

    private function getUrl(int $id): string
    {
        return URL::route('v1.user.address.delete', ['id' => $id]);
    }
}
