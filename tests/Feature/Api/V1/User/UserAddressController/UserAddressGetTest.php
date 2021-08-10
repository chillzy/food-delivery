<?php

namespace Tests\Feature\Api\V1\User\UserAddressController;

use App\Models\User;
use App\Models\UserAddress;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UserAddressGetTest extends TestCase
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

    public function testUserAddressSuccessfullyFetched(): void
    {
        $this->loginAs($this->user)
            ->getJson($this->getUrl($this->userAddress->id))
            ->assertOk()
            ->assertExactJson([
                'id' => $this->userAddress->id,
                'userId' => $this->user->id,
                'street' => $this->userAddress->street,
                'house' => $this->userAddress->house,
                'building' => $this->userAddress->building,
                'entrance' => $this->userAddress->entrance,
                'floor' => $this->userAddress->floor,
                'apartment' => $this->userAddress->apartment,
                'intercom' => $this->userAddress->intercom,
                'comment' => $this->userAddress->comment,
            ]);
    }

    public function testUserAddressFetchingFailed(): void
    {
        $this->getJson($this->getUrl($this->userAddress->id))->assertUnauthorized();

        /** @var User $anotherUser */
        $anotherUser = UserFactory::new()->create();

        /** @var UserAddress $anotherUserAddress */
        $anotherUserAddress = $anotherUser->addresses->first();

        $this->loginAs($this->user)
            ->getJson($this->getUrl($anotherUserAddress->id))
            ->assertForbidden();
    }

    private function getUrl(int $id): string
    {
        return URL::route('v1.user.address.get', ['id' => $id]);
    }
}
