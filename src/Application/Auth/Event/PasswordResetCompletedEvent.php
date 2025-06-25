<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\Event;

use CompanyOS\Domain\ValueObject\Uuid;

final class PasswordResetCompletedEvent
{
    public function __construct(
        public readonly Uuid $userId,
        public readonly string $email,
        public readonly string $resetToken,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 