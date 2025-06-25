<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\DTO;

final class RequestPasswordResetRequest
{
    public function __construct(
        public readonly string $email
    ) {
    }
} 