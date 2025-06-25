<?php

namespace CompanyOS\Application\Role\Command;

class AssignRoleToUserCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly string $roleId
    ) {
    }
} 