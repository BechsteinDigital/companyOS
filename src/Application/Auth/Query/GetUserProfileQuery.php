<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\Query;

use CompanyOS\Application\Query\Query;
use CompanyOS\Domain\ValueObject\Uuid;

final class GetUserProfileQuery implements Query
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