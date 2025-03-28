<?php
namespace Apie\ApieBundle\DependencyInjection;

use Apie\ApieBundle\Interfaces\ApieContextService;
use Apie\CmsApiDropdownOption\DropdownOptionProvider\DropdownOptionProviderInterface;
use Apie\Common\DependencyInjection\ApieConfigFileLocator;
use Apie\Common\Interfaces\RouteDefinitionProviderInterface;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\FileStorage\InlineStorage;
use Apie\DoctrineEntityDatalayer\Commands\ApieUpdateIdfCommand;
use Apie\DoctrineEntityDatalayer\EntityReindexer;
use Apie\DoctrineEntityDatalayer\IndexStrategy\BackgroundIndexStrategy;
use Apie\DoctrineEntityDatalayer\IndexStrategy\DirectIndexStrategy;
use Apie\DoctrineEntityDatalayer\IndexStrategy\IndexAfterResponseIsSentStrategy;
use Apie\DoctrineEntityDatalayer\IndexStrategy\IndexStrategyInterface;
use Apie\DoctrineEntityDatalayer\OrmBuilder;
use Apie\Faker\Interfaces\ApieClassFaker;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Loads all services into symfony. Which services depend on the apie configuration set up.
 */
final class ApieExtension extends Extension
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $dependencies = [
        'enable_common_plugin' => [
            'apie_common_plugin.yaml',
        ],
        'enable_cms' => [
            'common.yaml',
            'cms.yaml',
            'html_builders.yaml',
            'serializer.yaml',
            'sf_cms.yaml',
        ],
        'enable_cms_dropdown' => [
             'common.yaml',
             'cms_dropdown.yaml',
        ],
        'enable_core' => [
            'core.yaml',
            'sf_core.yaml',
        ],
        'enable_doctrine_entity_converter' => [
            'core.yaml',
            'doctrine_entity_converter.yaml',
        ],
        'enable_doctrine_entity_datalayer' => [
            'core.yaml',
            'doctrine_entity_converter.yaml',
            'doctrine_entity_datalayer.yaml',
        ],
        'enable_doctrine_bundle_connection' => [
            'core.yaml',
            'doctrine_entity_converter.yaml',
            'doctrine_entity_datalayer.yaml',
            'sf_doctrine.yaml',
        ],
        'enable_console' => [
            'common.yaml',
            'console.yaml',
            'serializer.yaml',
        ],
        'enable_maker' => [
            'maker.yaml',
            "sf_maker.yaml",
        ],
        'enable_profiler' => [
            'sf_profiler.yaml',
        ],
        'enable_security' => [
            'common.yaml',
            'serializer.yaml',
            'security.yaml',
        ],
        'enable_rest_api' => [
            'common.yaml',
            'rest_api.yaml',
            'schema_generator.yaml',
            'serializer.yaml',
        ],
        'enable_faker' => [
            'faker.yaml',
        ],
        'enable_twig_template_layout_renderer' => [
            'twig_template_layout_renderer.yaml',
        ],
    ];

    /**
     * @param array<string, mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new ApieConfigFileLocator(__DIR__.'/../../resources/config'));
        $loader->load('services.yaml');
        $loader->load('psr7.yaml');
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('apie.encryption_key', $config['encryption_key'] ?? null);
        $container->setParameter('apie.bounded_contexts', $config['bounded_contexts']);
        $container->setParameter('apie.scan_bounded_contexts', $config['scan_bounded_contexts'] ?? []);
        $container->setParameter('apie.storage', $config['storage'] ? : [['class' => InlineStorage::class]]);
        $container->setParameter('apie.datalayers', $config['datalayers'] ?? []);
        $container->setParameter('apie.cms.asset_folders', $config['cms']['asset_folders'] ?? []);
        $container->setParameter('apie.cms.dashboard_template', $config['cms']['dashboard_template'] ?? '@Apie/dashboard.html.twig');
        $container->setParameter('apie.cms.error_template', $config['cms']['error_template'] ?? '@Apie/error.html.twig');
        $container->setParameter('apie.cms.base_url', rtrim($config['cms']['base_url'] ?? '/cms', '/'));
        $container->setParameter('apie.doctrine.build_once', $config['doctrine']['build_once'] ?? false);
        $container->setParameter('apie.doctrine.connection_params', $config['doctrine']['connection_params'] ?? []);
        $container->setParameter('apie.doctrine.run_migrations', $config['doctrine']['run_migrations'] ?? false);
        $container->setParameter('apie.rest_api.base_url', rtrim($config['rest_api']['base_url'] ?? '/api', '/'));
        if (($config['enable_maker'] ?? false) && is_array($config['maker'] ?? null)) {
            $container->setParameter('apie.maker', $config['maker']);
        } else {
            $container->setParameter('apie.maker', null);
        }
        if ($config['enable_doctrine_entity_datalayer']) {
            $type = $config['doctrine']['indexing']['type'] ?? 'direct';
            $container->addAliases([
                IndexStrategyInterface::class => match($type) {
                    'direct' => DirectIndexStrategy::class,
                    'late' => IndexAfterResponseIsSentStrategy::class,
                    'background' => BackgroundIndexStrategy::class,
                    default => $config['doctrine']['indexing']['service'] ?? DirectIndexStrategy::class,
                },
            ]);
            if ($type === 'background') {
                $definition = new Definition(ApieUpdateIdfCommand::class);
                $definition->setArguments([
                    new Reference(EntityReindexer::class),
                    new Reference(OrmBuilder::class)
                ]);
                $definition->addTag('console.command');
                $container->addDefinitions([ApieUpdateIdfCommand::class => $definition]);
            }
        }
        $loaded = [];
        foreach ($this->dependencies as $configName => $dependencyList) {
            if ($config[$configName]) {
                foreach ($dependencyList as $dependency) {
                    if (!isset($loaded[$dependency])) {
                        $loaded[$dependency] = true;
                        $loader->load($dependency);
                    }
                }
            }
        }

        $container->registerForAutoconfiguration(ContextBuilderInterface::class)
            ->addTag('apie.core.context_builder');
        $container->registerForAutoconfiguration(ApieContextService::class)
            ->addTag('apie.context');
        $container->registerForAutoconfiguration(RouteDefinitionProviderInterface::class)
            ->addTag('apie.common.route_definition');
        $container->registerForAutoconfiguration(ApieDatalayer::class)
            ->addTag('apie.datalayer');
        $container->registerForAutoconfiguration(ApieClassFaker::class)
            ->addTag('apie.faker');
        if ($config['enable_cms']) {
            $container->registerForAutoconfiguration(FormComponentProviderInterface::class)
                ->addTag(FormComponentProviderInterface::class);
        }
        if ($config['enable_cms_dropdown']) {
            $container->registerForAutoconfiguration(DropdownOptionProviderInterface::class)
                ->addTag(DropdownOptionProviderInterface::class);
        }
    }
}
