<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\Event;

use CompanyOS\Domain\ValueObject\Uuid;

final class TokenRevokedEvent
{
    public function __construct(
        public readonly Uuid $userId,
        public readonly string $accessToken,
        public readonly string $clientId,
        public readonly string $reason,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 