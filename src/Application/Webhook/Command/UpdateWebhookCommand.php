<?php

namespace CompanyOS\Domain\Webhook\Application\Command;

use CompanyOS\Application\Command\CommandInterface;
use CompanyOS\Domain\Shared\ValueObject\Uuid;

class UpdateWebhookCommand implements CommandInterface
{
    public function __construct(
        public readonly Uuid $id,
        public readonly string $name,
        public readonly string $url,
        public readonly array $events,
        public readonly ?string $secret = null
    ) {
    }
} 