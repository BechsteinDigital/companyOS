<?php

namespace CompanyOS\Application\Webhook\Command;

use CompanyOS\Application\Command\CommandInterface;

class CreateWebhookCommand implements CommandInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $url,
        public readonly array $events,
        public readonly ?string $secret = null
    ) {
    }
} 