<?php

namespace Tests\Feature\Api\V1\User\UserAddressController;

use App\Models\User;
use App\Models\UserAddress;
use Database\Factories\UserAddressFactory;
use Database\Factories\UserFactory;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UserAddressCreateAndUpdateTest extends TestCase
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

    public function testUserAddressSuccessfullyCreated(): void
    {
        $data = [
            'street' => $this->faker->streetName,
            'house' => $this->faker->numberBetween(1, 32767),
            'building' => null,
            'entrance' => null,
            'floor' => null,
            'apartment' => null,
            'intercom' => null,
            'comment' => null,
        ];

        $response = $this->loginAs($this->user)->postJson($this->getCreateUrl(), $data)->assertCreated();

        $this->assertDatabaseHas(UserAddress::class, [
            'id' => $response['id'],
            'street' => $response['street'],
            'house' => $response['house'],
        ]);

        $response->assertExactJson([
            'id' => $response['id'],
            'userId' => $this->user->id,
            'street' => $data['street'],
            'house' => $data['house'],
            'building' => null,
            'entrance' => null,
            'floor' => null,
            'apartment' => null,
            'intercom' => null,
            'comment' => null,
        ]);
    }

    public function testUserAddressCreationFailed(): void
    {
        $validStreet = 'улица Пушкина';
        $validHouse = 5;

        $this->postJson($this->getCreateUrl(), [
            'street' => $validStreet,
            'house' => $validHouse,
        ])->assertUnauthorized();
    }

    /**
     * @dataProvider invalidDataProvider
     * @param mixed $street
     * @param mixed $house
     * @param null|mixed $property
     */
    public function testUserAddressCreationWithInvalidDataFailed($street, $house, $property = null): void
    {
        $data = [
            'street' => $street,
            'house' => $house,
            'building' => $this->userAddress->building,
            'entrance' => $this->userAddress->entrance,
            'floor' => $this->userAddress->floor,
            'apartment' => $this->userAddress->apartment,
            'intercom' => $this->userAddress->intercom,
            'comment' => $this->userAddress->comment,
        ];

        if (isset($property)) {
            $data = array_merge($data, [$property['name'] => $property['value']]);
        }

        $this->loginAs($this->user)
            ->postJson($this->getCreateUrl(), $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUserAddressSuccessfullyUpdated(): void
    {
        $data = [
            'street' => $this->faker->streetName,
            'house' => $this->faker->numberBetween(1, 32767),
            'building' => $this->userAddress->building,
            'entrance' => $this->userAddress->entrance,
            'floor' => $this->userAddress->floor,
            'apartment' => $this->userAddress->apartment,
            'intercom' => $this->userAddress->intercom,
            'comment' => $this->userAddress->comment,
        ];

        $response = $this->loginAs($this->user)
            ->putJson($this->getUpdateUrl($this->userAddress->id), $data)
            ->assertOk();

        $this->assertDatabaseHas(UserAddress::class, [
            'user_id' => $this->user->id,
            'street' => $data['street'],
            'house' => $data['house'],
            'building' => $this->userAddress->building,
            'entrance' => $this->userAddress->entrance,
            'floor' => $this->userAddress->floor,
            'apartment' => $this->userAddress->apartment,
            'intercom' => $this->userAddress->intercom,
            'comment' => $this->userAddress->comment,
        ]);

        $response->assertExactJson([
            'id' => $this->userAddress->id,
            'userId' => $this->user->id,
            'street' => $data['street'],
            'house' => $data['house'],
            'building' => $this->userAddress->building,
            'entrance' => $this->userAddress->entrance,
            'floor' => $this->userAddress->floor,
            'apartment' => $this->userAddress->apartment,
            'intercom' => $this->userAddress->intercom,
            'comment' => $this->userAddress->comment,
        ]);
    }

    public function testUserAddressUpdatingFailed(): void
    {
        $validStreet = 'улица Пушкина';
        $validHouse = 5;

        $this->putJson($this->getUpdateUrl($this->userAddress->id), [
            'street' => $validStreet,
            'house' => $validHouse,
            'building' => $this->userAddress->building,
            'entrance' => $this->userAddress->entrance,
            'floor' => $this->userAddress->floor,
            'apartment' => $this->userAddress->apartment,
            'intercom' => $this->userAddress->intercom,
            'comment' => $this->userAddress->comment,
        ])->assertUnauthorized();

        $notExistingAddressId = $this->user->addresses->pluck('id')->sum();
        $notExistingAddress = UserAddressFactory::new()->make(['id' => $notExistingAddressId]);
        $this->loginAs($this->user)
            ->putJson($this->getUpdateUrl($notExistingAddressId), [
                'street' => $validStreet,
                'house' => $validHouse,
                'building' => $this->userAddress->building,
                'entrance' => $this->userAddress->entrance,
                'floor' => $this->userAddress->floor,
                'apartment' => $this->userAddress->apartment,
                'intercom' => $this->userAddress->intercom,
                'comment' => $this->userAddress->comment,
            ])->assertNotFound();

        $this->assertDatabaseMissing(UserAddress::class, $notExistingAddress->toArray());

        /** @var User $anotherUser */
        $anotherUser = UserFactory::new()->create();

        /** @var UserAddress $anotherUserAddress */
        $anotherUserAddress = $anotherUser->addresses->first();

        $this->loginAs($this->user)
            ->putJson($this->getUpdateUrl($anotherUserAddress->id), [
                'street' => $validStreet,
                'house' => $validHouse,
                'building' => $this->userAddress->building,
                'entrance' => $this->userAddress->entrance,
                'floor' => $this->userAddress->floor,
                'apartment' => $this->userAddress->apartment,
                'intercom' => $this->userAddress->intercom,
                'comment' => $this->userAddress->comment,
            ])->assertForbidden();

        $this->assertDatabaseHas(UserAddress::class, $anotherUserAddress->toArray());
    }

    /**
     * @dataProvider invalidDataProvider
     * @param mixed $street
     * @param mixed $house
     * @param null|mixed $property
     */
    public function testUserAddressUpdatingWithInvalidDataFailed($street, $house, $property = null): void
    {
        $data = [
            'street' => $street,
            'house' => $house,
        ];

        if (isset($property)) {
            $data = array_merge($data, [$property['name'] => $property['value']]);
        }

        $this->loginAs($this->user)
            ->putJson($this->getUpdateUrl($this->userAddress->id), $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function invalidDataProvider(): array
    {
        $faker = Factory::create();

        return [
            'invalid street [null]' => [null, 1],
            'invalid street [not string]' => [1, 5],
            'invalid house [null]' => ['ул. Пушкина', null],
            'invalid house [not int]' => ['ул. Пушкина', 'д. Колотушкина'],
            'invalid house [negative int]' => ['ул. Пушкина', -5],

            'invalid building [too big]' => ['ул. Пушкина', 5, ['name' => 'building', 'value' => 346234623462346]],
            'invalid building [not int]' => ['ул. Пушкина', 5, ['name' => 'building', 'value' => 'к. 56']],
            'invalid building [negative int]' => ['ул. Пушкина', 5, ['name' => 'building', 'value' => -53]],

            'invalid entrance [too big]' => ['ул. Пушкина', 5, ['name' => 'entrance', 'value' => 346234623462346]],
            'invalid entrance [not int]' => ['ул. Пушкина', 5, ['name' => 'entrance', 'value' => 'п. 25']],
            'invalid entrance [negative int]' => ['ул. Пушкина', 5, ['name' => 'entrance', 'value' => -1]],

            'invalid floor [too big]' => ['ул. Пушкина', 5, ['name' => 'floor', 'value' => 346234623462346]],
            'invalid floor [not int]' => ['ул. Пушкина', 5, ['name' => 'floor', 'value' => 'э. 25']],
            'invalid floor [negative int]' => ['ул. Пушкина', 5, ['name' => 'floor', 'value' => -14]],

            'invalid apartment [too big]' => ['ул. Пушкина', 5, ['name' => 'apartment', 'value' => 346234623462346]],
            'invalid apartment [not int]' => ['ул. Пушкина', 5, ['name' => 'apartment', 'value' => 'кв. 132']],
            'invalid apartment [negative int]' => ['ул. Пушкина', 5, ['name' => 'apartment', 'value' => -355]],

            'invalid intercom [too long]' => [
                'ул. Пушкина',
                5,
                ['name' => 'intercom', 'value' => $faker->realTextBetween(51)],
            ],
            'invalid intercom [not string' => ['ул. Пушкина', 5, ['name' => 'intercom', 'value' => 1]],

            'invalid comment [not string]' => ['ул. Пушкина', 5, ['name' => 'comment', 'value' => 1]],
            'invalid comment [too long]' => [
                'ул. Пушкина',
                5,
                ['name' => 'comment', 'value' => $faker->realTextBetween(256, 300)],
            ],
        ];
    }

    private function getCreateUrl(): string
    {
        return URL::route('v1.user.address.create');
    }

    private function getUpdateUrl(int $id): string
    {
        return URL::route('v1.user.address.update', ['id' => $id]);
    }
}
