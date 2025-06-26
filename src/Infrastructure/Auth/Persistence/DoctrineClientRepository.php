<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Auth\Persistence;

use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Entity\Client;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Repository\ClientRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface as LeagueClientRepositoryInterface;

final class DoctrineClientRepository implements ClientRepositoryInterface, LeagueClientRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function save(Client $client): void
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    public function findById(Uuid $id): ?Client
    {
        return $this->entityManager->getRepository(Client::class)->find($id->value());
    }

    public function findByClientId(string $clientId): ?Client
    {
        return $this->entityManager->getRepository(Client::class)->findOneBy(['clientId' => $clientId]);
    }

    public function findAll(?string $clientId = null, ?string $clientName = null): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('c')->from(Client::class, 'c');

        if ($clientId !== null) {
            $qb->andWhere('c.clientId = :clientId')
               ->setParameter('clientId', $clientId);
        }

        if ($clientName !== null) {
            $qb->andWhere('c.clientName LIKE :clientName')
               ->setParameter('clientName', '%' . $clientName . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function delete(Client $client): void
    {
        $this->entityManager->remove($client);
        $this->entityManager->flush();
    }

    // League OAuth2 Server Interface Implementation
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        return $this->entityManager->getRepository(Client::class)->findOneBy([
            'clientId' => $clientIdentifier,
            'isActive' => true
        ]);
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

        // Für Password Grant (Frontend)
        if ($grantType === 'password') {
            // Frontend-Client braucht kein Secret
            return !$client->isConfidential();
        }

        return false;
    }
} 