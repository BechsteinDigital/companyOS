<?php

namespace CompanyOS\Application\Settings\Command;

use CompanyOS\Application\Command\CommandInterface;

class AddSalutationCommand implements CommandInterface
{
    public function __construct(
        public readonly string $type,
        public readonly string $template
    ) {
    }
} 