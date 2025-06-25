<?php

namespace CompanyOS\Domain\Webhook\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateWebhookRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $name,

        #[Assert\NotBlank]
        #[Assert\Url]
        public readonly string $url,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        #[Assert\Count(min: 1)]
        public readonly array $events,

        #[Assert\Length(max: 255)]
        public readonly ?string $secret = null
    ) {
    }
} 