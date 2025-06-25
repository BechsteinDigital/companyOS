<?php

namespace CompanyOS\Domain\Role\Application\Command;

class AssignRoleToUserCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly string $roleId
    ) {
    }
} 