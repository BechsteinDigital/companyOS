<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RoleResponse',
    title: 'Role Response',
    description: 'Role data returned by the API'
)]
class RoleResponse
{
    public function __construct(
        #[OA\Property(
            description: 'Unique identifier of the role',
            example: '550e8400-e29b-41d4-a716-446655440000',
            format: 'uuid'
        )]
        public string $id,

        #[OA\Property(
            description: 'Name of the role',
            example: 'admin'
        )]
        public string $name,

        #[OA\Property(
            description: 'Display name of the role',
            example: 'Administrator'
        )]
        public string $displayName,

        #[OA\Property(
            description: 'Description of the role',
            example: 'Full system administrator with all permissions',
            nullable: true
        )]
        public ?string $description,

        #[OA\Property(
            description: 'List of permissions for this role',
            example: ['user.create', 'user.read', 'user.update', 'user.delete'],
            type: 'array',
            items: new OA\Items(type: 'string')
        )]
        public array $permissions,

        #[OA\Property(
            description: 'Whether the role is system-defined (cannot be deleted)',
            example: true
        )]
        public bool $isSystem,

        #[OA\Property(
            description: 'Number of users assigned to this role',
            example: 5
        )]
        public int $userCount,

        #[OA\Property(
            description: 'Date when the role was created',
            example: '2024-01-15T10:30:00+00:00',
            format: 'date-time'
        )]
        public \DateTimeImmutable $createdAt,

        #[OA\Property(
            description: 'Date when the role was last updated',
            example: '2024-01-15T10:30:00+00:00',
            format: 'date-time',
            nullable: true
        )]
        public ?\DateTimeImmutable $updatedAt = null
    ) {
    }
} 