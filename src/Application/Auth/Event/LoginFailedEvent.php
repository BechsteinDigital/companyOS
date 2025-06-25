<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\Event;

final class LoginFailedEvent
{
    public function __construct(
        public readonly string $email,
        public readonly string $clientId,
        public readonly string $reason,
        public readonly string $ipAddress,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 