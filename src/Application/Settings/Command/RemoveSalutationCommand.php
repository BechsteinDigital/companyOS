<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Settings\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\CommandInterface;

class RemoveSalutationCommand implements CommandInterface
{
    public function __construct(
        public readonly string $type
    ) {
    }
} 