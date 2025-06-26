<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Webhook\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\CommandInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

class DeleteWebhookCommand implements CommandInterface
{
    public function __construct(
        public readonly Uuid $id
    ) {
    }
} 