<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\DTO;

final class ResetPasswordRequest
{
    public function __construct(
        public readonly string $token,
        public readonly string $newPassword,
        public readonly string $confirmPassword
    ) {
    }
} 