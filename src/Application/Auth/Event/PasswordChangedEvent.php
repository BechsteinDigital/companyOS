<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\Event;

use CompanyOS\Domain\ValueObject\Uuid;

final class PasswordChangedEvent
{
    public function __construct(
        public readonly Uuid $userId,
        public readonly string $email,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 