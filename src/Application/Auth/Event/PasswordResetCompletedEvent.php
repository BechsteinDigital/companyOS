<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\Event;

use CompanyOS\Domain\Shared\ValueObject\Uuid;

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