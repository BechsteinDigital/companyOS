<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Role\Security;

use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\Role;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RoleVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['ROLE_EDIT', 'ROLE_DELETE', 'ROLE_ASSIGN']) && $subject instanceof Role;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        // Beispiel: Nur Admins dürfen Rollen bearbeiten
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }
        return false;
    }
} 