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
        $accessToken = new LeagueAccessTokenEntity();
        $accessToken->setClient($clientEntity);
        $accessToken->setScopes($scopes);
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
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