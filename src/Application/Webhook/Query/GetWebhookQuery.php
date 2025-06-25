<?php

namespace CompanyOS\Domain\Webhook\Application\Query;

use CompanyOS\Application\Query\Query;

class GetWebhookQuery implements Query
{
    public function __construct(
        public readonly string $id
    ) {
    }
} 