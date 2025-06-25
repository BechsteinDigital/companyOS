<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\Event;

use CompanyOS\Domain\Shared\ValueObject\Uuid;

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