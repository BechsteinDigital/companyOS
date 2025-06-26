<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Command;

class DeleteRoleCommand
{
    public function __construct(
        public readonly string $id
    ) {
    }
} 