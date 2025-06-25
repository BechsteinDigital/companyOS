<?php

namespace CompanyOS\Domain\Settings\Application\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'InitializeCompanySettingsRequest',
    title: 'Initialize Company Settings Request',
    description: 'Request to initialize company settings with basic information'
)]
class InitializeCompanySettingsRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Company name is required')]
        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'Company name', example: 'Musterfirma GmbH')]
        public readonly string $companyName,

        #[Assert\NotBlank(message: 'Street is required')]
        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'Street address', example: 'Musterstraße')]
        public readonly string $street,

        #[Assert\NotBlank(message: 'House number is required')]
        #[Assert\Length(max: 20)]
        #[OA\Property(description: 'House number', example: '123')]
        public readonly string $houseNumber,

        #[Assert\NotBlank(message: 'Postal code is required')]
        #[Assert\Length(max: 20)]
        #[OA\Property(description: 'Postal code', example: '12345')]
        public readonly string $postalCode,

        #[Assert\NotBlank(message: 'City is required')]
        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'City', example: 'Musterstadt')]
        public readonly string $city,

        #[Assert\NotBlank(message: 'Country is required')]
        #[Assert\Length(max: 100)]
        #[OA\Property(description: 'Country', example: 'Deutschland')]
        public readonly string $country,

        #[Assert\NotBlank(message: 'Email is required')]
        #[Assert\Email]
        #[OA\Property(description: 'Company email', example: 'info@musterfirma.de')]
        public readonly string $email,

        #[Assert\NotBlank(message: 'SMTP host is required')]
        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'SMTP host', example: 'smtp.musterfirma.de')]
        public readonly string $smtpHost,

        #[Assert\NotBlank(message: 'Email from address is required')]
        #[Assert\Email]
        #[OA\Property(description: 'Email from address', example: 'noreply@musterfirma.de')]
        public readonly string $emailFromAddress,

        #[Assert\NotBlank(message: 'Email from name is required')]
        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'Email from name', example: 'Musterfirma GmbH')]
        public readonly string $emailFromName
    ) {
    }
} 