<?php

namespace CompanyOS\Domain\Plugin\Application\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PluginResponse',
    title: 'Plugin Response',
    description: 'Plugin data returned by the API'
)]
class PluginResponse
{
    public function __construct(
        #[OA\Property(
            description: 'Unique identifier of the plugin',
            example: '550e8400-e29b-41d4-a716-446655440000',
            format: 'uuid'
        )]
        public string $id,

        #[OA\Property(
            description: 'Name of the plugin',
            example: 'my-awesome-plugin'
        )]
        public string $name,

        #[OA\Property(
            description: 'Version of the plugin',
            example: '1.0.0'
        )]
        public string $version,

        #[OA\Property(
            description: 'Author of the plugin',
            example: 'John Doe'
        )]
        public string $author,

        #[OA\Property(
            description: 'Whether the plugin is currently active',
            example: true
        )]
        public bool $isActive,

        #[OA\Property(
            description: 'Additional metadata for the plugin',
            example: ['description' => 'A great plugin', 'website' => 'https://example.com'],
            type: 'object',
            nullable: true
        )]
        public ?array $meta,

        #[OA\Property(
            description: 'Date when the plugin was installed',
            example: '2024-01-15T10:30:00+00:00',
            format: 'date-time'
        )]
        public \DateTimeImmutable $installedAt,

        #[OA\Property(
            description: 'Date when the plugin was last updated',
            example: '2024-01-15T10:30:00+00:00',
            format: 'date-time',
            nullable: true
        )]
        public ?\DateTimeImmutable $updatedAt = null
    ) {
    }
} 