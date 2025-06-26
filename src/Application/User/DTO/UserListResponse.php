<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\DTO;

use OpenApi\Attributes as OA;

class UserListResponse
{
    #[OA\Property(type: "array", items: new OA\Items(ref: UserResponse::class))]
    public array $users;
}