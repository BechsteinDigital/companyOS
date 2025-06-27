<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface as LeagueClientRepositoryInterface;

final class LeagueClientRepository implements LeagueClientRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $qb = $this->entityManager->getConnection()->createQueryBuilder();
        $qb->select('c.*')
           ->from('oauth2_client', 'c')
           ->where('c.identifier = :identifier')
           ->andWhere('c.active = :active')
           ->setParameter('identifier', $clientIdentifier)
           ->setParameter('active', 1);

        $result = $qb->executeQuery()->fetchAssociative();

        if (!$result) {
            return null;
        }

        return new LeagueClientEntity(
            $result['identifier'],
            $result['name'],
            $result['secret'],
            json_decode($result['redirect_uris'] ?? '[]', true),
            json_decode($result['grants'] ?? '[]', true),
            json_decode($result['scopes'] ?? '[]', true),
            (bool) $result['active'],
            (bool) $result['allow_plain_text_pkce']
        );
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $client = $this->getClientEntity($clientIdentifier);
        
        if (!$client) {
            return false;
        }

        // Für Client Credentials Grant
        if ($grantType === 'client_credentials') {
            return $client->isConfidential() && 
                   $client->getSecret() === $clientSecret;
        }

        // Für Password Grant
        if ($grantType === 'password') {
            // Password Grant braucht kein Secret
            return true;
        }

        return false;
    }
}

class LeagueClientEntity implements ClientEntityInterface
{
    use \League\OAuth2\Server\Entities\Traits\ClientTrait;
    use \League\OAuth2\Server\Entities\Traits\EntityTrait;

    public function __construct(
        string $identifier,
        string $name,
        ?string $secret,
        array $redirectUris,
        array $grants,
        array $scopes,
        bool $active,
        bool $allowPlainTextPkce
    ) {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->secret = $secret;
        $this->redirectUri = $redirectUris;
        $this->isConfidential = $secret !== null;
        $this->scopes = $scopes;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function getRedirectUri(): array
    {
        return $this->redirectUri;
    }

    public function isConfidential(): bool
    {
        return $this->isConfidential;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }
} 