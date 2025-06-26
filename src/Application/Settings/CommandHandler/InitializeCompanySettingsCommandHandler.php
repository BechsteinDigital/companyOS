<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Settings\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Settings\Command\InitializeCompanySettingsCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Entity\CompanySettings;
use CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventDispatcher;

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