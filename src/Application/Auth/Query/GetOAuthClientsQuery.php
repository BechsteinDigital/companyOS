<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;

final class GetOAuthClientsQuery implements Query
{
    public function __construct(
        private readonly ?string $clientId = null,
        private readonly ?string $clientName = null
    ) {
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }
} 