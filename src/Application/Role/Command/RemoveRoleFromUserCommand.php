<?php

namespace CompanyOS\Application\Role\Command;

class RemoveRoleFromUserCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly string $roleId
    ) {
    }
} 