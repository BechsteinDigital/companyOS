<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Webhook\Persistence;

use CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Entity\Webhook;
use CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Repository\WebhookRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineWebhookRepository implements WebhookRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(Webhook $webhook): void
    {
        $this->entityManager->persist($webhook);
        $this->entityManager->flush();
    }

    public function findById(Uuid $id): ?Webhook
    {
        return $this->entityManager->getRepository(Webhook::class)->find($id);
    }

    public function findByName(string $name): ?Webhook
    {
        return $this->entityManager->getRepository(Webhook::class)
            ->findOneBy(['name' => $name]);
    }

    public function findByUrl(string $url): ?Webhook
    {
        return $this->entityManager->getRepository(Webhook::class)
            ->findOneBy(['url' => $url]);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Webhook::class)
            ->findBy([], ['createdAt' => 'DESC']);
    }

    public function findByEvent(string $eventName): array
    {
        return $this->entityManager->getRepository(Webhook::class)
            ->createQueryBuilder('w')
            ->where('w.isActive = :active')
            ->andWhere('JSON_CONTAINS(w.events, :event) = 1')
            ->setParameter('active', true)
            ->setParameter('event', json_encode($eventName))
            ->getQuery()
            ->getResult();
    }

    public function findActive(): array
    {
        return $this->entityManager->getRepository(Webhook::class)
            ->findBy(['isActive' => true], ['createdAt' => 'DESC']);
    }

    public function delete(Webhook $webhook): void
    {
        $this->entityManager->remove($webhook);
        $this->entityManager->flush();
    }
} 