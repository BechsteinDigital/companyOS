<?php

namespace CompanyOS\Domain\Settings\Infrastructure\DependencyInjection;

use CompanyOS\Domain\Settings\Application\Command\AddSalutationCommand;
use CompanyOS\Domain\Settings\Application\Command\InitializeCompanySettingsCommand;
use CompanyOS\Domain\Settings\Application\Command\RemoveSalutationCommand;
use CompanyOS\Domain\Settings\Application\Command\UpdateCompanySettingsCommand;
use CompanyOS\Domain\Settings\Application\CommandHandler\AddSalutationCommandHandler;
use CompanyOS\Domain\Settings\Application\CommandHandler\InitializeCompanySettingsCommandHandler;
use CompanyOS\Domain\Settings\Application\CommandHandler\RemoveSalutationCommandHandler;
use CompanyOS\Domain\Settings\Application\CommandHandler\UpdateCompanySettingsCommandHandler;
use CompanyOS\Domain\Settings\Application\Controller\SettingsController;
use CompanyOS\Domain\Settings\Application\Query\GetCompanySettingsQuery;
use CompanyOS\Domain\Settings\Application\QueryHandler\GetCompanySettingsQueryHandler;
use CompanyOS\Domain\Settings\Application\Service\SettingsService;
use CompanyOS\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use CompanyOS\Domain\Settings\Infrastructure\Persistence\DoctrineCompanySettingsRepository;
use CompanyOS\Application\Command\CommandHandlerInterface;
use CompanyOS\Application\Query\QueryHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class SettingsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        // Register repositories
        $container->setAlias(CompanySettingsRepositoryInterface::class, DoctrineCompanySettingsRepository::class);

        // Register command handlers
        $container->register(InitializeCompanySettingsCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command.bus'])
            ->addTag('app.command_handler', ['command' => InitializeCompanySettingsCommand::class]);

        $container->register(UpdateCompanySettingsCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command.bus'])
            ->addTag('app.command_handler', ['command' => UpdateCompanySettingsCommand::class]);

        $container->register(AddSalutationCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command.bus'])
            ->addTag('app.command_handler', ['command' => AddSalutationCommand::class]);

        $container->register(RemoveSalutationCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command.bus'])
            ->addTag('app.command_handler', ['command' => RemoveSalutationCommand::class]);

        // Register query handlers
        $container->register(GetCompanySettingsQueryHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus'])
            ->addTag('app.query_handler', ['query' => GetCompanySettingsQuery::class]);

        // Register services
        $container->register(SettingsService::class)
            ->setPublic(true);

        // Register controllers
        $container->register(SettingsController::class)
            ->setPublic(true)
            ->addTag('controller.service_arguments');
    }
} 