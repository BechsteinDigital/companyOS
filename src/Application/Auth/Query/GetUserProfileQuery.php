<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

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