<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Settings\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Settings\Command\RemoveSalutationCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventDispatcher;

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