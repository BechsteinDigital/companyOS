<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface as LeagueAccessTokenRepositoryInterface;

final class LeagueAccessTokenRepository implements LeagueAccessTokenRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        error_log('[OAuth2] getNewToken aufgerufen:');
        error_log('[OAuth2] - Client: ' . $clientEntity->getIdentifier());
        error_log('[OAuth2] - Scopes: ' . json_encode($scopes));
        error_log('[OAuth2] - UserIdentifier: ' . ($userIdentifier ?? 'null'));
        
        $accessToken = new LeagueAccessTokenEntity();
        $accessToken->setClient($clientEntity);
        $accessToken->setScopes($scopes);
        $accessToken->setUserIdentifier($userIdentifier);

        error_log('[OAuth2] Neuer AccessToken erstellt: ' . $accessToken->getIdentifier());
        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        error_log('[OAuth2] persistNewAccessToken aufgerufen:');
        error_log('[OAuth2] - Token ID: ' . $accessTokenEntity->getIdentifier());
        error_log('[OAuth2] - Client: ' . $accessTokenEntity->getClient()->getIdentifier());
        error_log('[OAuth2] - User: ' . ($accessTokenEntity->getUserIdentifier() ?? 'null'));
        error_log('[OAuth2] - Scopes: ' . json_encode($accessTokenEntity->getScopes()));
        error_log('[OAuth2] - Expires: ' . $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s'));
        
        $qb = $this->entityManager->getConnection()->createQueryBuilder();
        $qb->insert('oauth2_access_token')
           ->values([
               'identifier' => ':identifier',
               'client_id' => ':client_id',
               'user_id' => ':user_id',
               'scopes' => ':scopes',
               'revoked' => ':revoked',
               'expires_at' => ':expires_at'
           ])
           ->setParameters([
               'identifier' => $accessTokenEntity->getIdentifier(),
               'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
               'user_id' => $accessTokenEntity->getUserIdentifier(),
               'scopes' => json_encode($accessTokenEntity->getScopes()),
               'revoked' => 0,
               'expires_at' => $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s')
           ]);

        $qb->executeQuery();
        error_log('[OAuth2] AccessToken erfolgreich in DB gespeichert');
    }

    public function revokeAccessToken($tokenId): void
    {
        $qb = $this->entityManager->getConnection()->createQueryBuilder();
        $qb->update('oauth2_access_token')
           ->set('revoked', ':revoked')
           ->where('identifier = :identifier')
           ->setParameter('revoked', 1)
           ->setParameter('identifier', $tokenId);

        $qb->executeQuery();
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        $qb = $this->entityManager->getConnection()->createQueryBuilder();
        $qb->select('revoked')
           ->from('oauth2_access_token', 't')
           ->where('t.identifier = :identifier')
           ->setParameter('identifier', $tokenId);

        $result = $qb->executeQuery()->fetchAssociative();

        return $result === false || (bool) $result['revoked'];
    }
}

class LeagueAccessTokenEntity implements AccessTokenEntityInterface
{
    use \League\OAuth2\Server\Entities\Traits\EntityTrait;
    use \League\OAuth2\Server\Entities\Traits\TokenEntityTrait;
    use \League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

    public function __construct()
    {
        $this->identifier = bin2hex(random_bytes(40));
        $this->expiryDateTime = new \DateTimeImmutable('+1 hour');
    }
} 