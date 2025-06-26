<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Command\Handler;

use CompanyOS\Bundle\CoreBundle\Application\Plugin\Command\InstallPluginCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Entity\Plugin;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Repository\PluginRepository;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandler;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

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