<?php

namespace Tests\Feature\Api\V1\User\UserAddressController;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UserAddressListTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
    }

    public function testUserAddressesSuccessfullyFetched(): void
    {
        $expectedResponse = [];
        foreach ($this->user->addresses as $address) {
            $expectedResponse[] = [
                'id' => $address->id,
                'userId' => $address->user_id,
                'street' => $address->street,
                'house' => $address->house,
                'building' => $address->building,
                'entrance' => $address->entrance,
                'floor' => $address->floor,
                'apartment' => $address->apartment,
                'intercom' => $address->intercom,
                'comment' => $address->comment,
            ];
        }

        $this->loginAs($this->user)
            ->getJson($this->getUrl())
            ->assertOk()
            ->assertExactJson($expectedResponse);
    }

    public function testUserAddressesFetchingFailed(): void
    {
        $this->getJson($this->getUrl())->assertUnauthorized();
    }

    private function getUrl(): string
    {
        return URL::route('v1.user.address.list');
    }
}
