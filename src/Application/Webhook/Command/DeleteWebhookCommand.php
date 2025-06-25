<?php

namespace CompanyOS\Application\Webhook\Command;

use CompanyOS\Application\Command\CommandInterface;
use CompanyOS\Domain\ValueObject\Uuid;

class DeleteWebhookCommand implements CommandInterface
{
    public function __construct(
        public readonly Uuid $id
    ) {
    }
} 