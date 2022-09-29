<?php
namespace Apie\ApieBundle\DependencyInjection;

use Apie\Cms\RouteDefinitions\CmsRouteDefinitionProvider;
use Apie\Console\ConsoleCommandFactory;
use Apie\Faker\ApieObjectFaker;
use Apie\RestApi\OpenApi\OpenApiGenerator;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('apie');

        $treeBuilder->getRootNode()->children()
            ->arrayNode('cms')
                ->children()
                    ->scalarNode('base_url')->defaultValue('/cms')->end()
                    ->scalarNode('dashboard_template')->defaultValue('@Apie/dashboard.html.twig')->end()
                ->end()
            ->end()
            ->arrayNode('rest_api')
                ->children()
                    ->scalarNode('base_url')->defaultValue('/api')->end()
                ->end()
            ->end()
            ->arrayNode('datalayers')
                ->children()
                    ->scalarNode('default_datalayer')->isRequired()->end()
                    ->arrayNode('context_mapping')
                        ->useAttributeAsKey('name')
                        ->arrayPrototype()
                            ->isRequired()
                            ->children()
                                ->scalarNode('default_datalayer')->isRequired()->end()
                                ->arrayNode('entity_mapping')
                                    ->useAttributeAsKey('class')
                                    ->scalarPrototype()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->booleanNode('enable_core')->defaultValue(true)->end()
            ->booleanNode('enable_cms')->defaultValue(class_exists(CmsRouteDefinitionProvider::class))->end()
            ->booleanNode('enable_faker')->defaultValue(class_exists(ApieObjectFaker::class))->end()
            ->booleanNode('enable_rest_api')->defaultValue(class_exists(OpenApiGenerator::class))->end()
            ->booleanNode('enable_console')->defaultValue(class_exists(ConsoleCommandFactory::class))->end()
            ->booleanNode('enable_security')->defaultValue(class_exists(SecurityBundle::class))->end()
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
