<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\Bundle\OAuth2ServerBundle\Model\AbstractClient;

#[ORM\Entity]
#[ORM\Table(name: 'oauth2_client')]
#[ORM\AttributeOverrides([
    new ORM\AttributeOverride(
        name: 'identifier',
        column: new ORM\Column(type: 'string', length: 32, nullable: false)
    )
])]
class Client extends AbstractClient
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    protected string $identifier;

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
        parent::__construct($name, $identifier, $secret);
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
} 