<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\DTO;

final class UserProfileResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly array $roles,
        public readonly string $createdAt,
        public readonly ?string $updatedAt = null
    ) {
    }
} 