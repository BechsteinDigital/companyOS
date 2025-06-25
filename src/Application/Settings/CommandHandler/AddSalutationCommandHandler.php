<?php

namespace CompanyOS\Domain\Settings\Application\CommandHandler;

use CompanyOS\Domain\Settings\Application\Command\AddSalutationCommand;
use CompanyOS\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use CompanyOS\Application\Command\CommandHandlerInterface;
use CompanyOS\Infrastructure\Event\DomainEventDispatcher;

class AddSalutationCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CompanySettingsRepositoryInterface $repository,
        private DomainEventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(AddSalutationCommand $command): void
    {
        $settings = $this->repository->find();
        
        if (!$settings) {
            throw new \InvalidArgumentException('Company settings not found. Please initialize first.');
        }

        $settings->addSalutation($command->type, $command->template);
        $this->repository->save($settings);

        // Dispatch domain events if needed
        $this->eventDispatcher->dispatchAll($settings);
    }
} 