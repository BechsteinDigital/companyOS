<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Command;

class UpdateRoleCommand
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $displayName,
        public readonly ?string $description,
        public readonly ?array $permissions
    ) {
    }
} 