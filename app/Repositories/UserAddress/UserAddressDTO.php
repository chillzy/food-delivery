<?php

namespace App\Repositories\UserAddress;

class UserAddressDTO
{
    public string $street;
    public int $house;
    public ?int $building;
    public ?int $entrance;
    public ?int $floor;
    public ?int $apartment;
    public ?string $intercom;
    public ?string $comment;

    public function __construct(string $street, int $house)
    {
        $this->street = $street;
        $this->house = $house;
    }

    public function withBuilding(?int $building): self
    {
        $this->building = $building;

        return $this;
    }

    public function withEntrance(?int $entrance): self
    {
        $this->entrance = $entrance;

        return $this;
    }

    public function withFloor(?int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function withApartment(?int $apartment): self
    {
        $this->apartment = $apartment;

        return $this;
    }

    public function withIntercom(?string $intercom): self
    {
        $this->intercom = $intercom;

        return $this;
    }

    public function withComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
