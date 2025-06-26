<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Event\UserCreated;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Event\UserUpdated;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Event\UserDeleted;
use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, UserEntityInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: 'email')]
    private Email $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $passwordHash;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    private array $domainEvents = [];

    public function __construct(
        Uuid $id,
        Email $email,
        string $firstName,
        string $lastName,
        ?string $passwordHash = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->passwordHash = $passwordHash;
        $this->isActive = true;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

        $this->addDomainEvent(new UserCreated($id));
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function update(
        ?Email $email = null,
        ?string $firstName = null,
        ?string $lastName = null
    ): void {
        if ($email !== null) {
            $this->email = $email;
        }
        if ($firstName !== null) {
            $this->firstName = $firstName;
        }
        if ($lastName !== null) {
            $this->lastName = $lastName;
        }

        $this->updatedAt = new \DateTimeImmutable();
        $this->addDomainEvent(new UserUpdated($this->getId()));
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
        $this->updatedAt = new \DateTimeImmutable();
        $this->addDomainEvent(new UserUpdated($this->getId()));
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTimeImmutable();
        $this->addDomainEvent(new UserUpdated($this->getId()));
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new \DateTimeImmutable();
        $this->addDomainEvent(new UserUpdated($this->getId()));
    }

    public function delete(): void
    {
        $this->addDomainEvent(new UserDeleted($this->getId()));
    }

    private function addDomainEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function getDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    // UserInterface Methoden
    public function getRoles(): array
    {
        // Standard-Rolle für alle User
        return ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email->getValue();
    }

    public function eraseCredentials(): void
    {
        // Nichts zu tun
    }

    public function getIdentifier(): string
    {
        return $this->id->getValue();
    }
} 