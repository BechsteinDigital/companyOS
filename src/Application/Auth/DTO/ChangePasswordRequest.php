<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\DTO;

final class ChangePasswordRequest
{
    public function __construct(
        public readonly string $userId,
        public readonly string $currentPassword,
        public readonly string $newPassword,
        public readonly string $confirmPassword
    ) {
    }
} 