<?php

namespace CompanyOS\Domain\User\Application\DTO;

use OpenApi\Attributes as OA;

class DeleteUserResponse
{
    #[OA\Property(type: "boolean", example: true)]
    public bool $success;

    #[OA\Property(type: "string", example: "User deleted successfully")]
    public string $message;
}