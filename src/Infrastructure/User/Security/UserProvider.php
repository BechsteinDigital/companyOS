<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\User\Security;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Email;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Versuche zuerst, den User über die ID zu finden (für OAuth2)
        if (Uuid::isValid($identifier)) {
            $user = $this->userRepository->findById(Uuid::fromString($identifier));
        } else {
            // Fallback: Suche über E-Mail
            $user = $this->userRepository->findByEmail(Email::fromString($identifier));
        }

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with identifier "%s" not found.', $identifier));
        }

        // Lade die Rollen für den User
        $this->loadUserRoles($user);

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('User must be an instance of ' . User::class);
        }

        // Lade den User neu aus der Datenbank
        $refreshedUser = $this->userRepository->findById($user->getId());
        
        if (!$refreshedUser) {
            throw new UserNotFoundException(sprintf('User with ID "%s" not found.', $user->getId()->getValue()));
        }

        // Lade die Rollen für den User
        $this->loadUserRoles($refreshedUser);

        return $refreshedUser;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    private function loadUserRoles(User $user): void
    {
        // Lade die Rollen aus der Datenbank
        $roles = $this->roleRepository->findUserRoles($user->getId()->getValue());
        
        // Konvertiere die Rollen in Symfony-Rollen-Format
        $symfonyRoles = ['ROLE_USER']; // Standard-Rolle für alle User
        
        foreach ($roles as $role) {
            $symfonyRoles[] = 'ROLE_' . strtoupper($role->name()->value());
        }
        
        // Setze die Rollen im User-Objekt
        $user->setRoles($symfonyRoles);
    }
} 