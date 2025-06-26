<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Webhook\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\CommandInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

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