<?php

namespace CompanyOS\Bundle\CoreBundle\Application\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="App API",
 *     version="1.0.0",
 *     description="App ist eine moderne, modulare Unternehmensplattform basierend auf Domain-Driven Design (DDD), Command Query Responsibility Segregation (CQRS) und Event-Driven Architecture.",
 *     @OA\Contact(
 *         name="App Development Team",
 *         email="dev@companyos.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Development Server"
 * )
 * 
 * @OA\Server(
 *     url="https://api.companyos.com",
 *     description="Production Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="BearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="JWT Token für API-Authentifizierung"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication and authorization operations"
 * )
 * 
 * @OA\Tag(
 *     name="Users",
 *     description="User management operations"
 * )
 * 
 * @OA\Tag(
 *     name="Plugins",
 *     description="Plugin management operations"
 * )
 * 
 * @OA\Tag(
 *     name="System",
 *     description="System and health check operations"
 * )
 */
class OpenApiInfo
{
    // Diese Klasse dient nur als Container für die OpenAPI-Annotationen
    // und wird nicht instanziiert
} 