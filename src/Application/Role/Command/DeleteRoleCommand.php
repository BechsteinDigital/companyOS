<?php

namespace CompanyOS\Application\Role\Command;

class DeleteRoleCommand
{
    public function __construct(
        public readonly string $id
    ) {
    }
} 