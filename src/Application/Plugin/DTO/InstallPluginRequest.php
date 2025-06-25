<?php

namespace CompanyOS\Application\Plugin\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'InstallPluginRequest',
    title: 'Install Plugin Request',
    description: 'Request data for installing a new plugin'
)]
class InstallPluginRequest
{
    public function __construct(
        #[OA\Property(
            description: 'Name of the plugin',
            example: 'my-awesome-plugin',
            maxLength: 255
        )]
        #[Assert\NotBlank(message: 'Plugin name is required')]
        #[Assert\Length(max: 255, maxMessage: 'Plugin name cannot be longer than {{ limit }} characters')]
        #[Assert\Regex(pattern: '/^[a-z0-9-]+$/', message: 'Plugin name can only contain lowercase letters, numbers and hyphens')]
        public string $name,

        #[OA\Property(
            description: 'Version of the plugin',
            example: '1.0.0',
            maxLength: 50
        )]
        #[Assert\NotBlank(message: 'Plugin version is required')]
        #[Assert\Length(max: 50, maxMessage: 'Plugin version cannot be longer than {{ limit }} characters')]
        #[Assert\Regex(pattern: '/^[0-9]+\.[0-9]+\.[0-9]+$/', message: 'Version must be in semantic versioning format (e.g., 1.0.0)')]
        public string $version,

        #[OA\Property(
            description: 'Author of the plugin',
            example: 'John Doe',
            maxLength: 255
        )]
        #[Assert\NotBlank(message: 'Plugin author is required')]
        #[Assert\Length(max: 255, maxMessage: 'Plugin author cannot be longer than {{ limit }} characters')]
        public string $author,

        #[OA\Property(
            description: 'Additional metadata for the plugin',
            example: ['description' => 'A great plugin', 'website' => 'https://example.com'],
            type: 'object',
            nullable: true
        )]
        #[Assert\Type(type: 'array', message: 'Meta must be an array')]
        public ?array $meta = null
    ) {
    }
} 