<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\Event;

use CompanyOS\Domain\ValueObject\Uuid;

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