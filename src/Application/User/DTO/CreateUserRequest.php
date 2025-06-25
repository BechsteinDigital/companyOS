<?php

namespace CompanyOS\Application\User\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: "CreateUserRequest",
    title: "Create User Request",
    description: "Request body for creating a new user",
    required: ["email", "firstName", "lastName"]
)]
class CreateUserRequest
{
    #[OA\Property(
        property: "email",
        type: "string",
        format: "email",
        description: "User's email address",
        example: "john.doe@example.com"
    )]
    #[Assert\NotBlank(message: "Email is required")]
    #[Assert\Email(message: "Invalid email format")]
    public string $email;

    #[OA\Property(
        property: "firstName",
        type: "string",
        description: "User's first name",
        example: "John",
        minLength: 1,
        maxLength: 255
    )]
    #[Assert\NotBlank(message: "First name is required")]
    #[Assert\Length(min: 1, max: 255, minMessage: "First name must be at least 1 character", maxMessage: "First name cannot be longer than 255 characters")]
    public string $firstName;

    #[OA\Property(
        property: "lastName",
        type: "string",
        description: "User's last name",
        example: "Doe",
        minLength: 1,
        maxLength: 255
    )]
    #[Assert\NotBlank(message: "Last name is required")]
    #[Assert\Length(min: 1, max: 255, minMessage: "Last name must be at least 1 character", maxMessage: "Last name cannot be longer than 255 characters")]
    public string $lastName;

    #[OA\Property(
        property: "password",
        type: "string",
        description: "User's password (optional, can be set later)",
        example: "securePassword123",
        minLength: 8
    )]
    #[Assert\Length(min: 8, minMessage: "Password must be at least 8 characters long")]
    public ?string $password = null;

    #[OA\Property(
        property: "roleIds",
        type: "array",
        items: new OA\Items(type: "string", format: "uuid"),
        description: "List of role IDs to assign to the user",
        example: ["550e8400-e29b-41d4-a716-446655440000", "550e8400-e29b-41d4-a716-446655440001"]
    )]
    #[Assert\Type(type: "array", message: "Role IDs must be an array")]
    #[Assert\All([
        new Assert\NotBlank(message: "Role ID cannot be empty"),
        new Assert\Uuid(message: "Invalid UUID format for role ID")
    ])]
    public array $roleIds = [];
}
