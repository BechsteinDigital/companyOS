<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\Bundle\OAuth2ServerBundle\Model\AbstractClient;

#[ORM\Entity]
#[ORM\Table(name: 'oauth2_client')]
class Client extends AbstractClient
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    protected string $identifier;

    #[ORM\Column(type: 'string')]
    protected string $name;

    #[ORM\Column(type: 'string')]
    protected string $secret;

    #[ORM\Column(type: 'json')]
    protected array $redirectUris;

    #[ORM\Column(type: 'json')]
    protected array $grants;

    #[ORM\Column(type: 'json')]
    protected array $scopes;

    #[ORM\Column(type: 'boolean')]
    protected bool $active;

    #[ORM\Column(type: 'boolean')]
    protected bool $allowPlainTextPkce;

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
        $this->identifier = $identifier;
        $this->name = $name;
        $this->secret = $secret;
        $this->redirectUris = $redirectUris;
        $this->grants = $grants;
        $this->scopes = $scopes;
        $this->active = $active;
        $this->allowPlainTextPkce = $allowPlainTextPkce;
    }
} 