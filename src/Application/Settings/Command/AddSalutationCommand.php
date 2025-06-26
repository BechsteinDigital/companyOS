<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Settings\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\CommandInterface;

class AddSalutationCommand implements CommandInterface
{
    public function __construct(
        public readonly string $type,
        public readonly string $template
    ) {
    }
} 