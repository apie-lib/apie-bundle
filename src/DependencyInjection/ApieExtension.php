<?php
namespace Apie\ApieBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ApieExtension extends Extension
{
    private array $dependencies = [
        'enable_rest_api' => [
            'rest_api.yaml',
            'schema_generator.yaml',
            'serializer.yaml',
        ]
    ];

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
