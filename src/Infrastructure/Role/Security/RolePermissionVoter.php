<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Role\Security;

use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RolePermissionVoter extends Voter
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Unterstützt alle Permission-Attribute
        return str_starts_with($attribute, 'PERMISSION_');
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user) {
            return false;
        }

        // Permission aus dem Attribute extrahieren (z.B. "PERMISSION_USER_CREATE" -> "user.create")
        $permission = strtolower(str_replace('PERMISSION_', '', $attribute));
        $permission = str_replace('_', '.', $permission);

        // User-ID aus dem User-Objekt extrahieren
        $userId = (string)$user->getId();
        
        // Rollen des Users laden
        $userRoles = $this->roleRepository->findUserRoles($userId);
        
        // Prüfen, ob eine der Rollen die benötigte Permission hat
        foreach ($userRoles as $role) {
            if ($role->permissions()->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
} 