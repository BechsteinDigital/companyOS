<?php

namespace CompanyOS\Application\Settings\Command;

use CompanyOS\Application\Command\CommandInterface;

class RemoveSalutationCommand implements CommandInterface
{
    public function __construct(
        public readonly string $type
    ) {
    }
} 