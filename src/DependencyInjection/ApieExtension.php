<?php
namespace Apie\ApieBundle\DependencyInjection;

use Apie\ApieBundle\Interfaces\ApieContextService;
use Apie\CmsApiDropdownOption\DropdownOptionProvider\DropdownOptionProviderInterface;
use Apie\Common\DependencyInjection\ApieConfigFileLocator;
use Apie\Common\Interfaces\RouteDefinitionProviderInterface;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Faker\Interfaces\ApieClassFaker;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Loads all services into symfony. Which services depend on the apie configuration set up.
 */
final class ApieExtension extends Extension
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $dependencies = [
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
        $container->setParameter('apie.bounded_contexts', $config['bounded_contexts']);
        $container->setParameter('apie.datalayers', $config['datalayers'] ?? []);
        $container->setParameter('apie.cms.asset_folders', $config['cms']['asset_folders'] ?? []);
        $container->setParameter('apie.cms.dashboard_template', $config['cms']['dashboard_template'] ?? '@Apie/dashboard.html.twig');
        $container->setParameter('apie.cms.error_template', $config['cms']['error_template'] ?? '@Apie/error.html.twig');
        $container->setParameter('apie.cms.base_url', rtrim($config['cms']['base_url'] ?? '/cms', '/'));
        $container->setParameter('apie.doctrine.build_once', $config['doctrine']['build_once'] ?? false);
        $container->setParameter('apie.doctrine.connection_params', $config['doctrine']['connection_params'] ?? []);
        $container->setParameter('apie.doctrine.run_migrations', $config['doctrine']['run_migrations'] ?? false);
        $container->setParameter('apie.rest_api.base_url', rtrim($config['rest_api']['base_url'] ?? '/api', '/'));
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
