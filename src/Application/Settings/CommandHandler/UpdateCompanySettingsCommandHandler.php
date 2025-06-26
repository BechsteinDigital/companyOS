<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Settings\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Settings\Command\UpdateCompanySettingsCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Entity\CompanySettings;
use CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventDispatcher;

class UpdateCompanySettingsCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CompanySettingsRepositoryInterface $repository,
        private DomainEventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(UpdateCompanySettingsCommand $command): void
    {
        $settings = $this->repository->find();
        
        if (!$settings) {
            throw new \InvalidArgumentException('Company settings not found. Please initialize first.');
        }

        // Update company information
        if ($command->companyName !== null || $command->legalName !== null || 
            $command->taxNumber !== null || $command->vatNumber !== null) {
            $settings->updateCompanyInfo(
                $command->companyName ?? $settings->getCompanyName(),
                $command->legalName,
                $command->taxNumber,
                $command->vatNumber
            );
        }

        // Update address
        if ($command->street !== null || $command->houseNumber !== null || 
            $command->postalCode !== null || $command->city !== null || 
            $command->country !== null || $command->state !== null) {
            $settings->updateAddress(
                $command->street ?? $settings->getStreet(),
                $command->houseNumber ?? $settings->getHouseNumber(),
                $command->postalCode ?? $settings->getPostalCode(),
                $command->city ?? $settings->getCity(),
                $command->country ?? $settings->getCountry(),
                $command->state
            );
        }

        // Update contact information
        if ($command->email !== null || $command->phone !== null || 
            $command->fax !== null || $command->website !== null || 
            $command->supportEmail !== null) {
            $settings->updateContactInfo(
                $command->email ?? $settings->getEmail(),
                $command->phone,
                $command->fax,
                $command->website,
                $command->supportEmail
            );
        }

        // Update localization
        if ($command->defaultLanguage !== null || $command->defaultCurrency !== null || 
            $command->timezone !== null || $command->dateFormat !== null || 
            $command->timeFormat !== null || $command->numberFormat !== null) {
            $settings->updateLocalization(
                $command->defaultLanguage ?? $settings->getDefaultLanguage(),
                $command->defaultCurrency ?? $settings->getDefaultCurrency(),
                $command->timezone ?? $settings->getTimezone(),
                $command->dateFormat ?? $settings->getDateFormat(),
                $command->timeFormat ?? $settings->getTimeFormat(),
                $command->numberFormat ?? $settings->getNumberFormat()
            );
        }

        // Update system settings
        if ($command->systemName !== null || $command->logoUrl !== null || 
            $command->defaultUserRole !== null || $command->sessionTimeout !== null || 
            $command->maintenanceMode !== null) {
            $settings->updateSystemSettings(
                $command->systemName ?? $settings->getSystemName(),
                $command->logoUrl,
                $command->defaultUserRole ?? $settings->getDefaultUserRole(),
                $command->sessionTimeout ?? $settings->getSessionTimeout(),
                $command->maintenanceMode ?? $settings->isMaintenanceMode()
            );
        }

        // Update email settings
        if ($command->emailFromName !== null || $command->emailFromAddress !== null || 
            $command->emailReplyTo !== null || $command->smtpHost !== null || 
            $command->smtpPort !== null || $command->smtpEncryption !== null || 
            $command->smtpUsername !== null || $command->smtpPassword !== null) {
            $settings->updateEmailSettings(
                $command->emailFromName ?? $settings->getEmailFromName(),
                $command->emailFromAddress ?? $settings->getEmailFromAddress(),
                $command->emailReplyTo,
                $command->smtpHost ?? $settings->getSmtpHost(),
                $command->smtpPort ?? $settings->getSmtpPort(),
                $command->smtpEncryption ?? $settings->getSmtpEncryption(),
                $command->smtpUsername,
                $command->smtpPassword
            );
        }

        // Update salutations
        if ($command->salutations !== null) {
            $settings->updateSalutations($command->salutations);
        }

        $this->repository->save($settings);

        // Dispatch domain events if needed
        $this->eventDispatcher->dispatchAll($settings);
    }
} 