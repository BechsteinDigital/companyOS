<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\Bundle\OAuth2ServerBundle\Model\AbstractClient;

#[ORM\Entity]
#[ORM\Table(name: 'oauth2_client')]
class Client extends AbstractClient
{
    public function __construct(
        string $identifier,
        string $name,
        string $secret,
        array $redirectUris = [],
        array $grants = [],
        array $scopes = [],
        bool $active = true,
        bool $allowPlainTextPkce = false
    ) {
        parent::__construct($identifier, $name, $secret, $redirectUris, $grants, $scopes, $active, $allowPlainTextPkce);
    }
} 