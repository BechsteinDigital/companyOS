<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\Event;

use CompanyOS\Domain\ValueObject\Uuid;

final class TokenRefreshedEvent
{
    public function __construct(
        public readonly Uuid $userId,
        public readonly string $oldAccessToken,
        public readonly string $newAccessToken,
        public readonly string $refreshToken,
        public readonly string $clientId,
        public readonly array $scopes,
        public readonly \DateTimeImmutable $expiresAt,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 