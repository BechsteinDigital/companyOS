<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Event;

final class PasswordResetRequestedEvent
{
    public function __construct(
        public readonly string $email,
        public readonly string $resetToken,
        public readonly \DateTimeImmutable $expiresAt,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 