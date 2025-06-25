<?php

namespace CompanyOS\Domain\Settings\Application\Command;

use CompanyOS\Application\Command\CommandInterface;

class AddSalutationCommand implements CommandInterface
{
    public function __construct(
        public readonly string $type,
        public readonly string $template
    ) {
    }
} 