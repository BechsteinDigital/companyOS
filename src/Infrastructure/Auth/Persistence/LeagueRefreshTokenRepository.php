<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface as LeagueRefreshTokenRepositoryInterface;

final class LeagueRefreshTokenRepository implements LeagueRefreshTokenRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        error_log('[OAuth2] getNewRefreshToken aufgerufen');
        $refreshToken = new LeagueRefreshTokenEntity();
        error_log('[OAuth2] Neuer RefreshToken erstellt: ' . $refreshToken->getIdentifier());
        return $refreshToken;
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        error_log('[OAuth2] persistNewRefreshToken aufgerufen:');
        error_log('[OAuth2] - RefreshToken ID: ' . $refreshTokenEntity->getIdentifier());
        error_log('[OAuth2] - AccessToken ID: ' . $refreshTokenEntity->getAccessToken()->getIdentifier());
        error_log('[OAuth2] - Expires: ' . $refreshTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s'));
        
        $qb = $this->entityManager->getConnection()->createQueryBuilder();
        $qb->insert('oauth2_refresh_token')
           ->values([
               'identifier' => ':identifier',
               'access_token_identifier' => ':access_token_identifier',
               'revoked' => ':revoked',
               'expires_at' => ':expires_at'
           ])
           ->setParameters([
               'identifier' => $refreshTokenEntity->getIdentifier(),
               'access_token_identifier' => $refreshTokenEntity->getAccessToken()->getIdentifier(),
               'revoked' => 0,
               'expires_at' => $refreshTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s')
           ]);

        $qb->executeQuery();
        error_log('[OAuth2] RefreshToken erfolgreich in DB gespeichert');
    }

    public function revokeRefreshToken($tokenId): void
    {
        $qb = $this->entityManager->getConnection()->createQueryBuilder();
        $qb->update('oauth2_refresh_token')
           ->set('revoked', ':revoked')
           ->where('identifier = :identifier')
           ->setParameter('revoked', 1)
           ->setParameter('identifier', $tokenId);

        $qb->executeQuery();
    }

    public function isRefreshTokenRevoked($tokenId): bool
    {
        $qb = $this->entityManager->getConnection()->createQueryBuilder();
        $qb->select('revoked')
           ->from('oauth2_refresh_token', 't')
           ->where('t.identifier = :identifier')
           ->setParameter('identifier', $tokenId);

        $result = $qb->executeQuery()->fetchAssociative();

        return $result === false || (bool) $result['revoked'];
    }
}

class LeagueRefreshTokenEntity implements RefreshTokenEntityInterface
{
    use \League\OAuth2\Server\Entities\Traits\EntityTrait;
    use \League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

    public function __construct()
    {
        $this->identifier = bin2hex(random_bytes(40));
        $this->expiryDateTime = new \DateTimeImmutable('+1 month');
    }
} 