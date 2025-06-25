<?php

namespace CompanyOS\Application\User\DTO;

use OpenApi\Attributes as OA;
use CompanyOS\Application\Role\DTO\RoleResponse;

#[OA\Schema(
    schema: "UserResponse",
    title: "User Response",
    description: "User data response"
)]
class UserResponse
{
    #[OA\Property(
        property: "id",
        type: "string",
        format: "uuid",
        description: "User's unique identifier",
        example: "550e8400-e29b-41d4-a716-446655440000"
    )]
    public string $id;

    #[OA\Property(
        property: "email",
        type: "string",
        format: "email",
        description: "User's email address",
        example: "john.doe@example.com"
    )]
    public string $email;

    #[OA\Property(
        property: "firstName",
        type: "string",
        description: "User's first name",
        example: "John"
    )]
    public string $firstName;

    #[OA\Property(
        property: "lastName",
        type: "string",
        description: "User's last name",
        example: "Doe"
    )]
    public string $lastName;

    #[OA\Property(
        property: "fullName",
        type: "string",
        description: "User's full name",
        example: "John Doe"
    )]
    public string $fullName;

    #[OA\Property(
        property: "isActive",
        type: "boolean",
        description: "Whether the user account is active",
        example: true
    )]
    public bool $isActive;

    #[OA\Property(
        property: "createdAt",
        type: "string",
        format: "date-time",
        description: "User creation timestamp",
        example: "2024-01-15T10:30:00+00:00"
    )]
    public string $createdAt;

    #[OA\Property(
        property: "updatedAt",
        type: "string",
        format: "date-time",
        description: "User last update timestamp",
        example: "2024-01-15T10:30:00+00:00"
    )]
    public string $updatedAt;

    #[OA\Property(
        property: "roles",
        type: "array",
        items: new OA\Items(ref: "#/components/schemas/RoleResponse"),
        description: "List of roles assigned to the user"
    )]
    public array $roles = [];

    public function __construct(
        string $id,
        string $email,
        string $firstName,
        string $lastName,
        string $fullName,
        bool $isActive,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
        array $roles = []
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->fullName = $fullName;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt->format('c');
        $this->updatedAt = $updatedAt->format('c');
        $this->roles = $roles;
    }
}
