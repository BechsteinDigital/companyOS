<?php

namespace CompanyOS\Application\Role\Command;

class CreateRoleCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $displayName,
        public readonly ?string $description,
        public readonly array $permissions
    ) {
    }
} 