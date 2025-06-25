<?php

namespace CompanyOS\Domain\Role\Application\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'CreateRoleRequest',
    title: 'Create Role Request',
    description: 'Request data for creating a new role'
)]
class CreateRoleRequest
{
    public function __construct(
        #[OA\Property(
            description: 'Name of the role',
            example: 'admin',
            maxLength: 100
        )]
        #[Assert\NotBlank(message: 'Role name is required')]
        #[Assert\Length(max: 100, maxMessage: 'Role name cannot be longer than {{ limit }} characters')]
        #[Assert\Regex(pattern: '/^[a-z0-9_-]+$/', message: 'Role name can only contain lowercase letters, numbers, underscores and hyphens')]
        public string $name,

        #[OA\Property(
            description: 'Display name of the role',
            example: 'Administrator',
            maxLength: 255
        )]
        #[Assert\NotBlank(message: 'Role display name is required')]
        #[Assert\Length(max: 255, maxMessage: 'Role display name cannot be longer than {{ limit }} characters')]
        public string $displayName,

        #[OA\Property(
            description: 'Description of the role',
            example: 'Full system administrator with all permissions',
            maxLength: 1000
        )]
        #[Assert\Length(max: 1000, maxMessage: 'Role description cannot be longer than {{ limit }} characters')]
        public ?string $description = null,

        #[OA\Property(
            description: 'List of permissions for this role',
            example: ['user.create', 'user.read', 'user.update', 'user.delete'],
            type: 'array',
            items: new OA\Items(type: 'string')
        )]
        #[Assert\Type(type: 'array', message: 'Permissions must be an array')]
        #[Assert\All([
            new Assert\NotBlank(message: 'Permission cannot be empty'),
            new Assert\Length(max: 255, maxMessage: 'Permission cannot be longer than {{ limit }} characters')
        ])]
        public array $permissions = []
    ) {
    }
} 