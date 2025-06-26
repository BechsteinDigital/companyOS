<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Settings\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'UpdateCompanySettingsRequest',
    title: 'Update Company Settings Request',
    description: 'Request to update company settings (all fields are optional)'
)]
class UpdateCompanySettingsRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Company name is required')]
        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'Company name', example: 'Musterfirma GmbH')]
        public readonly ?string $companyName = null,

        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'Legal company name', example: 'Musterfirma Gesellschaft mit beschränkter Haftung')]
        public readonly ?string $legalName = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(description: 'Tax number', example: '123/456/78901')]
        public readonly ?string $taxNumber = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(description: 'VAT number', example: 'DE123456789')]
        public readonly ?string $vatNumber = null,

        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'Street address', example: 'Musterstraße')]
        public readonly ?string $street = null,

        #[Assert\Length(max: 20)]
        #[OA\Property(description: 'House number', example: '123')]
        public readonly ?string $houseNumber = null,

        #[Assert\Length(max: 20)]
        #[OA\Property(description: 'Postal code', example: '12345')]
        public readonly ?string $postalCode = null,

        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'City', example: 'Musterstadt')]
        public readonly ?string $city = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(description: 'Country', example: 'Deutschland')]
        public readonly ?string $country = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(description: 'State/Province', example: 'Bayern')]
        public readonly ?string $state = null,

        #[Assert\Email]
        #[OA\Property(description: 'Company email', example: 'info@musterfirma.de')]
        public readonly ?string $email = null,

        #[Assert\Length(max: 50)]
        #[OA\Property(description: 'Phone number', example: '+49 123 456789')]
        public readonly ?string $phone = null,

        #[Assert\Length(max: 50)]
        #[OA\Property(description: 'Fax number', example: '+49 123 456788')]
        public readonly ?string $fax = null,

        #[Assert\Length(max: 255)]
        #[Assert\Url]
        #[OA\Property(description: 'Website URL', example: 'https://www.musterfirma.de')]
        public readonly ?string $website = null,

        #[Assert\Email]
        #[OA\Property(description: 'Support email', example: 'support@musterfirma.de')]
        public readonly ?string $supportEmail = null,

        #[Assert\Length(max: 10)]
        #[OA\Property(description: 'Default language', example: 'de')]
        public readonly ?string $defaultLanguage = null,

        #[Assert\Length(max: 10)]
        #[OA\Property(description: 'Default currency', example: 'EUR')]
        public readonly ?string $defaultCurrency = null,

        #[Assert\Length(max: 50)]
        #[OA\Property(description: 'Timezone', example: 'Europe/Berlin')]
        public readonly ?string $timezone = null,

        #[Assert\Length(max: 20)]
        #[OA\Property(description: 'Date format', example: 'd.m.Y')]
        public readonly ?string $dateFormat = null,

        #[Assert\Length(max: 20)]
        #[OA\Property(description: 'Time format', example: 'H:i')]
        public readonly ?string $timeFormat = null,

        #[Assert\Length(max: 20)]
        #[OA\Property(description: 'Number format', example: '1.234,56')]
        public readonly ?string $numberFormat = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(description: 'System name', example: 'App')]
        public readonly ?string $systemName = null,

        #[Assert\Length(max: 255)]
        #[Assert\Url]
        #[OA\Property(description: 'Logo URL', example: 'https://example.com/logo.png')]
        public readonly ?string $logoUrl = null,

        #[Assert\Length(max: 100)]
        #[OA\Property(description: 'Default user role', example: 'user')]
        public readonly ?string $defaultUserRole = null,

        #[Assert\PositiveOrZero]
        #[OA\Property(description: 'Session timeout in seconds', example: 3600)]
        public readonly ?int $sessionTimeout = null,

        #[OA\Property(description: 'Maintenance mode enabled', example: false)]
        public readonly ?bool $maintenanceMode = null,

        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'Email from name', example: 'Musterfirma GmbH')]
        public readonly ?string $emailFromName = null,

        #[Assert\Email]
        #[OA\Property(description: 'Email from address', example: 'noreply@musterfirma.de')]
        public readonly ?string $emailFromAddress = null,

        #[Assert\Email]
        #[OA\Property(description: 'Email reply-to address', example: 'reply@musterfirma.de')]
        public readonly ?string $emailReplyTo = null,

        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'SMTP host', example: 'smtp.musterfirma.de')]
        public readonly ?string $smtpHost = null,

        #[Assert\Positive]
        #[OA\Property(description: 'SMTP port', example: 587)]
        public readonly ?int $smtpPort = null,

        #[Assert\Length(max: 10)]
        #[OA\Property(description: 'SMTP encryption', example: 'tls')]
        public readonly ?string $smtpEncryption = null,

        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'SMTP username', example: 'smtp@musterfirma.de')]
        public readonly ?string $smtpUsername = null,

        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'SMTP password')]
        public readonly ?string $smtpPassword = null,

        #[OA\Property(description: 'Salutations configuration', type: 'object')]
        public readonly ?array $salutations = null
    ) {
    }
} 