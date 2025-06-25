<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\Event;

use CompanyOS\Domain\ValueObject\Uuid;

final class UserLoggedInEvent
{
    public function __construct(
        public readonly Uuid $userId,
        public readonly string $email,
        public readonly string $clientId,
        public readonly array $scopes,
        public readonly string $accessToken,
        public readonly string $refreshToken,
        public readonly \DateTimeImmutable $expiresAt,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 