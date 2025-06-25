<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Infrastructure\Persistence;

use CompanyOS\Domain\Auth\Domain\Entity\AccessToken;
use CompanyOS\Domain\Auth\Domain\Repository\AccessTokenRepositoryInterface;
use CompanyOS\Domain\Shared\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface as LeagueAccessTokenRepositoryInterface;

final class DoctrineAccessTokenRepository implements AccessTokenRepositoryInterface, LeagueAccessTokenRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        $accessToken = new AccessToken();
        $accessToken->setClient($clientEntity);
        $accessToken->setScopes($scopes);
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $this->entityManager->persist($accessTokenEntity);
        $this->entityManager->flush();
    }

    public function revokeAccessToken($tokenId): void
    {
        $accessToken = $this->entityManager->getRepository(AccessToken::class)->findOneBy([
            'identifier' => $tokenId
        ]);

        if ($accessToken) {
            $accessToken->setIsRevoked(true);
            $this->entityManager->flush();
        }
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        $accessToken = $this->entityManager->getRepository(AccessToken::class)->findOneBy([
            'identifier' => $tokenId
        ]);

        return $accessToken === null || $accessToken->isRevoked();
    }

    public function save(AccessToken $accessToken): void
    {
        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();
    }

    public function findById(Uuid $id): ?AccessToken
    {
        return $this->entityManager->getRepository(AccessToken::class)->find($id->value());
    }

    public function findByToken(string $token): ?AccessToken
    {
        return $this->entityManager->getRepository(AccessToken::class)->findOneBy(['identifier' => $token]);
    }

    public function findActiveByUserId(Uuid $userId): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        
        return $qb->select('at')
            ->from(AccessToken::class, 'at')
            ->where('at.userId = :userId')
            ->andWhere('at.isRevoked = :isRevoked')
            ->andWhere('at.expiryDateTime > :now')
            ->setParameter('userId', $userId->value())
            ->setParameter('isRevoked', false)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function delete(AccessToken $accessToken): void
    {
        $this->entityManager->remove($accessToken);
        $this->entityManager->flush();
    }

    public function deleteExpired(): void
    {
        $qb = $this->entityManager->createQueryBuilder();
        
        $qb->delete(AccessToken::class, 'at')
            ->where('at.expiryDateTime < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
} 