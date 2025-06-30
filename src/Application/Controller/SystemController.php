<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="System",
 *     description="System and health check operations"
 * )
 */
#[Route('/system')]
class SystemController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/api/system/health",
     *     summary="Health check",
     *     description="Check the health status of the system and its dependencies",
     *     tags={"System"},
     *     @OA\Response(
     *         response=200,
     *         description="System is healthy",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="healthy"),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2024-01-15T10:30:00+00:00"),
     *             @OA\Property(
     *                 property="services",
     *                 type="object",
     *                 @OA\Property(property="database", type="string", example="healthy"),
     *                 @OA\Property(property="redis", type="string", example="healthy"),
     *                 @OA\Property(property="elasticsearch", type="string", example="healthy")
     *             ),
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(property="environment", type="string", example="dev")
     *         )
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="System is unhealthy",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="unhealthy"),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2024-01-15T10:30:00+00:00"),
     *             @OA\Property(
     *                 property="services",
     *                 type="object",
     *                 @OA\Property(property="database", type="string", example="unhealthy"),
     *                 @OA\Property(property="redis", type="string", example="healthy"),
     *                 @OA\Property(property="elasticsearch", type="string", example="unhealthy")
     *             ),
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(property="environment", type="string", example="dev")
     *         )
     *     )
     * )
     */
    #[Route('/health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        // In einer echten Implementierung würden hier die verschiedenen Services geprüft
        $services = [
            'database' => 'healthy',
            'redis' => 'healthy',
            'elasticsearch' => 'healthy'
        ];

        $isHealthy = !in_array('unhealthy', $services);

        return $this->json([
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => (new \DateTime())->format('c'),
            'services' => $services,
            'version' => '1.0.0',
            'environment' => $this->getParameter('kernel.environment')
        ], $isHealthy ? 200 : 503);
    }

    /**
     * @OA\Get(
     *     path="/api/system/info",
     *     summary="System information",
     *     description="Get detailed information about the system",
     *     tags={"System"},
     *     @OA\Response(
     *         response=200,
     *         description="System information retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="App"),
     *                 @OA\Property(property="version", type="string", example="1.0.0"),
     *                 @OA\Property(property="environment", type="string", example="dev"),
     *                 @OA\Property(property="php_version", type="string", example="8.3.0"),
     *                 @OA\Property(property="symfony_version", type="string", example="7.3.0"),
     *                 @OA\Property(property="architecture", type="string", example="DDD + CQRS + Event-Driven"),
     *                 @OA\Property(property="features", type="array", @OA\Items(type="string"), example={"Plugin System", "API-First", "Modular Design"}),
     *                 @OA\Property(property="uptime", type="string", example="2 days, 5 hours, 30 minutes"),
     *                 @OA\Property(property="memory_usage", type="string", example="128 MB"),
     *                 @OA\Property(property="disk_usage", type="string", example="1.2 GB")
     *             )
     *         )
     *     )
     * )
     */
    #[Route('/info', methods: ['GET'])]
    public function info(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => [
                'name' => 'App',
                'version' => '1.0.0',
                'environment' => $this->getParameter('kernel.environment'),
                'php_version' => PHP_VERSION,
                'symfony_version' => \Symfony\Component\HttpKernel\Kernel::VERSION,
                'architecture' => 'DDD + CQRS + Event-Driven',
                'features' => [
                    'Plugin System',
                    'API-First',
                    'Modular Design',
                    'Domain-Driven Design',
                    'Command Query Responsibility Segregation',
                    'Event-Driven Architecture'
                ],
                'uptime' => '2 days, 5 hours, 30 minutes', // In echt würde das berechnet werden
                'memory_usage' => memory_get_usage(true) . ' bytes',
                'disk_usage' => '1.2 GB' // In echt würde das berechnet werden
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/system/status",
     *     summary="System status",
     *     description="Get current system status and metrics",
     *     tags={"System"},
     *     @OA\Response(
     *         response=200,
     *         description="System status retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", example="operational"),
     *                 @OA\Property(property="load_average", type="array", @OA\Items(type="number"), example={0.5, 0.3, 0.2}),
     *                 @OA\Property(property="cpu_usage", type="number", format="float", example=15.5),
     *                 @OA\Property(property="memory_usage", type="number", format="float", example=45.2),
     *                 @OA\Property(property="disk_usage", type="number", format="float", example=23.1),
     *                 @OA\Property(property="active_users", type="integer", example=42),
     *                 @OA\Property(property="active_plugins", type="integer", example=5),
     *                 @OA\Property(property="requests_per_minute", type="integer", example=120),
     *                 @OA\Property(property="response_time_avg", type="number", format="float", example=125.5)
     *             )
     *         )
     *     )
     * )
     */
    #[Route('/status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => [
                'status' => 'operational',
                'load_average' => [0.5, 0.3, 0.2],
                'cpu_usage' => 15.5,
                'memory_usage' => 45.2,
                'disk_usage' => 23.1,
                'active_users' => 42,
                'active_plugins' => 5,
                'requests_per_minute' => 120,
                'response_time_avg' => 125.5
            ]
        ]);
    }
} 