<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Event;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

final class UserLoggedOutEvent
{
    public function __construct(
        public readonly Uuid $userId,
        public readonly string $accessToken,
        public readonly string $clientId,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 