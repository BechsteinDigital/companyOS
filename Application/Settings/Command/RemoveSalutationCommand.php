<?php

namespace CompanyOS\Domain\Settings\Application\Command;

use CompanyOS\Application\Command\CommandInterface;

class RemoveSalutationCommand implements CommandInterface
{
    public function __construct(
        public readonly string $type
    ) {
    }
} 