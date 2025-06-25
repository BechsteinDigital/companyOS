<?php

namespace CompanyOS\Application\Plugin\Command\Handler;

use CompanyOS\Application\Plugin\Command\InstallPluginCommand;
use CompanyOS\Domain\Plugin\Domain\Entity\Plugin;
use CompanyOS\Domain\Plugin\Domain\Repository\PluginRepository;
use CompanyOS\Application\Command\CommandHandler;
use CompanyOS\Domain\ValueObject\Uuid;

class InstallPluginCommandHandler implements CommandHandler
{
    public function __construct(
        private PluginRepository $pluginRepository
    ) {
    }

    public function __invoke(InstallPluginCommand $command): void
    {
        if ($this->pluginRepository->existsByName($command->name)) {
            throw new \InvalidArgumentException('Plugin with this name already exists');
        }

        $plugin = new Plugin(
            Uuid::random(),
            $command->name,
            $command->version,
            $command->author,
            $command->meta
        );

        $this->pluginRepository->save($plugin);
    }
} 