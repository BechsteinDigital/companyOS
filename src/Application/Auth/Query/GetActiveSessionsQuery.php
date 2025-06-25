<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\Query;

use CompanyOS\Application\Query\Query;
use CompanyOS\Domain\Shared\ValueObject\Uuid;

final class GetActiveSessionsQuery implements Query
{
    public function __construct(
        private readonly Uuid $userId
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }
} 