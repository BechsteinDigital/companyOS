<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Webhook\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;

class GetWebhookQuery implements Query
{
    public function __construct(
        public readonly string $id
    ) {
    }
} 