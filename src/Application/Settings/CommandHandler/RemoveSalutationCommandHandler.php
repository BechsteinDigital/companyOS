<?php

namespace CompanyOS\Domain\Settings\Application\CommandHandler;

use CompanyOS\Domain\Settings\Application\Command\RemoveSalutationCommand;
use CompanyOS\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use CompanyOS\Application\Command\CommandHandlerInterface;
use CompanyOS\Infrastructure\Event\DomainEventDispatcher;

class RemoveSalutationCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CompanySettingsRepositoryInterface $repository,
        private DomainEventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(RemoveSalutationCommand $command): void
    {
        $settings = $this->repository->find();
        
        if (!$settings) {
            throw new \InvalidArgumentException('Company settings not found. Please initialize first.');
        }

        $settings->removeSalutation($command->type);
        $this->repository->save($settings);

        // Dispatch domain events if needed
        $this->eventDispatcher->dispatchAll($settings);
    }
} 