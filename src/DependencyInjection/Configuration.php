<?php
namespace Apie\ApieBundle\DependencyInjection;

use Apie\Faker\ApieObjectFaker;
use Apie\RestApi\Actions\CreateObjectAction;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('apie');

        $treeBuilder->getRootNode()->children()
            ->arrayNode('rest_api')
                ->children()
                    ->scalarNode('base_url')->defaultValue('/api')->end()
                ->end()
            ->end()
            ->booleanNode('enable_faker')->defaultValue(class_exists(ApieObjectFaker::class))->end()
            ->booleanNode('enable_rest_api')->defaultValue(class_exists(CreateObjectAction::class))->end()
            ->arrayNode('bounded_contexts')
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('entities_folder')->isRequired()->end()
                        ->scalarNode('entities_namespace')->isRequired()->end()
                        ->scalarNode('actions_folder')->isRequired()->end()
                        ->scalarNode('actions_namespace')->isRequired()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
