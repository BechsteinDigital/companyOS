<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence;

use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Entity\RefreshToken;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class DoctrineRefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new RefreshToken();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $this->entityManager->persist($refreshTokenEntity);
        $this->entityManager->flush();
    }

    public function revokeRefreshToken($tokenId): void
    {
        $refreshToken = $this->entityManager->getRepository(RefreshToken::class)->findOneBy([
            'identifier' => $tokenId
        ]);

        if ($refreshToken) {
            $refreshToken->setIsRevoked(true);
            $this->entityManager->flush();
        }
    }

    public function isRefreshTokenRevoked($tokenId): bool
    {
        $refreshToken = $this->entityManager->getRepository(RefreshToken::class)->findOneBy([
            'identifier' => $tokenId
        ]);

        return $refreshToken === null || $refreshToken->isRevoked();
    }
} 