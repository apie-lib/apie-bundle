<?php
namespace Apie\ApieBundle\DependencyInjection;

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
