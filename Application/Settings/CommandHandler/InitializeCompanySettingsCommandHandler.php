<?php

namespace CompanyOS\Domain\Settings\Application\CommandHandler;

use CompanyOS\Domain\Settings\Application\Command\InitializeCompanySettingsCommand;
use CompanyOS\Domain\Settings\Domain\Entity\CompanySettings;
use CompanyOS\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use CompanyOS\Application\Command\CommandHandlerInterface;
use CompanyOS\Domain\Shared\Event\DomainEventDispatcher;

class InitializeCompanySettingsCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CompanySettingsRepositoryInterface $repository,
        private DomainEventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(InitializeCompanySettingsCommand $command): void
    {
        if ($this->repository->exists()) {
            throw new \InvalidArgumentException('Company settings already exist');
        }

        $settings = new CompanySettings(
            $command->companyName,
            $command->street,
            $command->houseNumber,
            $command->postalCode,
            $command->city,
            $command->country,
            $command->email,
            $command->smtpHost,
            $command->emailFromAddress,
            $command->emailFromName
        );

        $this->repository->save($settings);

        // Dispatch domain events if needed
        $this->eventDispatcher->dispatchAll($settings);
    }
} 