<?php

namespace CompanyOS\Domain\Role\Application\Command;

class DeleteRoleCommand
{
    public function __construct(
        public readonly string $id
    ) {
    }
} 