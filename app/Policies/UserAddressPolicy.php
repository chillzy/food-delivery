<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAddress;

class UserAddressPolicy
{
    public function get(User $requestingUser, UserAddress $address): bool
    {
        return $this->doesUserOwnAddress($requestingUser, $address);
    }

    public function update(User $requestingUser, UserAddress $address): bool
    {
        return $this->doesUserOwnAddress($requestingUser, $address);
    }

    public function delete(User $requestingUser, UserAddress $address): bool
    {
        return $this->doesUserOwnAddress($requestingUser, $address);
    }

    private function doesUserOwnAddress(User $user, UserAddress $address): bool
    {
        return $address->user_id === $user->id;
    }
}
