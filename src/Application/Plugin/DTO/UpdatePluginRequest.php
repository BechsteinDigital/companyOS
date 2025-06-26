<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'UpdatePluginRequest',
    title: 'Update Plugin Request',
    description: 'Request data for updating an existing plugin'
)]
class UpdatePluginRequest
{
    public function __construct(
        #[OA\Property(
            description: 'Version of the plugin',
            example: '1.1.0',
            maxLength: 50
        )]
        #[Assert\Length(max: 50, maxMessage: 'Plugin version cannot be longer than {{ limit }} characters')]
        #[Assert\Regex(pattern: '/^[0-9]+\.[0-9]+\.[0-9]+$/', message: 'Version must be in semantic versioning format (e.g., 1.0.0)')]
        public ?string $version = null,

        #[OA\Property(
            description: 'Author of the plugin',
            example: 'John Doe',
            maxLength: 255
        )]
        #[Assert\Length(max: 255, maxMessage: 'Plugin author cannot be longer than {{ limit }} characters')]
        public ?string $author = null,

        #[OA\Property(
            description: 'Additional metadata for the plugin',
            example: ['description' => 'An updated great plugin', 'website' => 'https://example.com'],
            type: 'object',
            nullable: true
        )]
        #[Assert\Type(type: 'array', message: 'Meta must be an array')]
        public ?array $meta = null
    ) {
    }
} 