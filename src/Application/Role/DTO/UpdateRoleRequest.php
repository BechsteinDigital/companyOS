<?php

namespace CompanyOS\Domain\Role\Application\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'UpdateRoleRequest',
    title: 'Update Role Request',
    description: 'Request data for updating an existing role'
)]
class UpdateRoleRequest
{
    public function __construct(
        #[OA\Property(
            description: 'Display name of the role',
            example: 'Administrator',
            maxLength: 255
        )]
        #[Assert\Length(max: 255, maxMessage: 'Role display name cannot be longer than {{ limit }} characters')]
        public ?string $displayName = null,

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
        public ?array $permissions = null
    ) {
    }
} 