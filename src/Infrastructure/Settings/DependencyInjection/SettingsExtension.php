<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Settings\DependencyInjection;

use CompanyOS\Bundle\CoreBundle\Application\Settings\Command\AddSalutationCommand;
use CompanyOS\Bundle\CoreBundle\Application\Settings\Command\InitializeCompanySettingsCommand;
use CompanyOS\Bundle\CoreBundle\Application\Settings\Command\RemoveSalutationCommand;
use CompanyOS\Bundle\CoreBundle\Application\Settings\Command\UpdateCompanySettingsCommand;
use CompanyOS\Bundle\CoreBundle\Application\Settings\CommandHandler\AddSalutationCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Settings\CommandHandler\InitializeCompanySettingsCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Settings\CommandHandler\RemoveSalutationCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Settings\CommandHandler\UpdateCompanySettingsCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Settings\Controller\SettingsController;
use CompanyOS\Bundle\CoreBundle\Application\Settings\Query\GetCompanySettingsQuery;
use CompanyOS\Bundle\CoreBundle\Application\Settings\QueryHandler\GetCompanySettingsQueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Settings\Service\SettingsService;
use CompanyOS\Bundle\CoreBundle\Domain\Settings\Domain\Repository\CompanySettingsRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Settings\Persistence\DoctrineCompanySettingsRepository;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandlerInterface;
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