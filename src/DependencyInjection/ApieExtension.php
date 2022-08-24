<?php
namespace Apie\ApieBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
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
        'enable_core' => [
            'core.yaml',
        ],
        'enable_console' => [
            'common.yaml',
            'console.yaml',
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
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../resources/config'));
        $loader->load('services.yaml');
        $loader->load('psr7.yaml');
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('apie.bounded_contexts', $config['bounded_contexts']);
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
    }
}
