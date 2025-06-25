<?php

namespace CompanyOS\Domain\Role\Application\Command;

class RemoveRoleFromUserCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly string $roleId
    ) {
    }
} 