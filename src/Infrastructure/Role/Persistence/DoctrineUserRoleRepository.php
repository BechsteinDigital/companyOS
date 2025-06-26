<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Role\Persistence;

use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\Role;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserRoleRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function assignRoleToUser(User $user, Role $role): void
    {
        $user->assignRole($role);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function removeRoleFromUser(User $user, Role $role): void
    {
        $user->removeRole($role);
        $this->em->persist($user);
        $this->em->flush();
    }
} 