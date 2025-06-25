<?php

namespace CompanyOS\Domain\Role\Infrastructure\Persistence;

use CompanyOS\Domain\Role\Domain\Entity\Role;
use CompanyOS\Domain\Role\Domain\Entity\UserRole;
use CompanyOS\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleId;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleName;
use CompanyOS\Domain\User\Domain\Entity\User;
use CompanyOS\Domain\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineRoleRepository implements RoleRepositoryInterface
{
    private EntityRepository $roleRepository;
    private EntityRepository $userRoleRepository;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->roleRepository = $entityManager->getRepository(Role::class);
        $this->userRoleRepository = $entityManager->getRepository(UserRole::class);
    }

    public function findById(RoleId $id): ?Role
    {
        return $this->roleRepository->find($id->value());
    }

    public function findByName(RoleName $name): ?Role
    {
        return $this->roleRepository->findOneBy(['name' => $name->value()]);
    }

    public function findAll(bool $includeSystem = true, ?string $search = null): array
    {
        $criteria = [];
        
        if (!$includeSystem) {
            $criteria['isSystem'] = false;
        }

        $roles = $this->roleRepository->findBy($criteria);

        if ($search) {
            $roles = array_filter($roles, function (Role $role) use ($search) {
                return stripos($role->name()->value(), $search) !== false ||
                       stripos($role->displayName()->value(), $search) !== false;
            });
        }

        return array_values($roles);
    }

    public function save(Role $role): void
    {
        $this->entityManager->persist($role);
        $this->entityManager->flush();
    }

    public function delete(Role $role): void
    {
        if (!$role->canBeDeleted()) {
            throw new \InvalidArgumentException('System roles cannot be deleted');
        }

        // Remove all user assignments first
        $userRoles = $this->userRoleRepository->findBy(['role' => $role]);
        foreach ($userRoles as $userRole) {
            $this->entityManager->remove($userRole);
        }

        $this->entityManager->remove($role);
        $this->entityManager->flush();
    }

    public function findUserRoles(string $userId): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('r')
           ->from(Role::class, 'r')
           ->join(UserRole::class, 'ur', 'WITH', 'ur.role = r')
           ->where('ur.user = :userId')
           ->setParameter('userId', $userId);

        return $qb->getQuery()->getResult();
    }

    public function assignRoleToUser(RoleId $roleId, Uuid $userId): void
    {
        if ($this->isRoleAssignedToUser($userId, $roleId)) {
            throw new \InvalidArgumentException('Role is already assigned to user');
        }

        $user = $this->entityManager->getRepository(User::class)->find((string)$userId);
        $role = $this->findById($roleId);

        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        if (!$role) {
            throw new \InvalidArgumentException('Role not found');
        }

        $userRole = new UserRole($user, $role);
        $this->entityManager->persist($userRole);
        $this->entityManager->flush();
    }

    public function removeRoleFromUser(Uuid $userId, RoleId $roleId): void
    {
        $userRole = $this->userRoleRepository->findOneBy([
            'user' => (string)$userId,
            'role' => $roleId->value()
        ]);

        if (!$userRole) {
            throw new \InvalidArgumentException('Role is not assigned to user');
        }

        $this->entityManager->remove($userRole);
        $this->entityManager->flush();
    }

    public function removeAllUserRoles(Uuid $userId): void
    {
        $userRoles = $this->userRoleRepository->findBy([
            'user' => (string)$userId
        ]);

        foreach ($userRoles as $userRole) {
            $this->entityManager->remove($userRole);
        }

        $this->entityManager->flush();
    }

    public function isRoleAssignedToUser(Uuid $userId, RoleId $roleId): bool
    {
        $userRole = $this->userRoleRepository->findOneBy([
            'user' => (string)$userId,
            'role' => $roleId->value()
        ]);

        return $userRole !== null;
    }

    public function getUserCount(RoleId $roleId): int
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('COUNT(ur.id)')
           ->from(UserRole::class, 'ur')
           ->where('ur.role = :roleId')
           ->setParameter('roleId', $roleId->value());

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
} 