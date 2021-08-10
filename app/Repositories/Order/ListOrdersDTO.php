<?php

namespace App\Repositories\Order;

class ListOrdersDTO
{
    public int $limit;
    public int $offset;
    public ?array $statuses;

    public function __construct(
        int $limit = OrderRepositoryInterface::DEFAULT_LIST_LIMIT,
        int $offset = OrderRepositoryInterface::DEFAULT_LIST_OFFSET,
        ?array $statuses = OrderRepositoryInterface::DEFAULT_LIST_STATUSES
    ) {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->statuses = $statuses;
    }
}
