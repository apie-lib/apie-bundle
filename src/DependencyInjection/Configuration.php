<?php

namespace Apie\ApieBundle\DependencyInjection;

use Apie\Common\Config\Configuration as CommonConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class Configuration extends CommonConfiguration
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $tree = parent::getConfigTreeBuilder();
        $tree->getRootNode()
            ->children()
            ->booleanNode('enable_profiler')->defaultValue(true)->end()
            ->booleanNode('enable_doctrine_bundle_connection')->defaultValue(class_exists('Apie\DoctrineEntityDatalayer\DoctrineEntityDatalayer') && class_exists('Doctrine\Bundle\DoctrineBundle\DoctrineBundle'))->end()
            ->booleanNode('enable_security')->defaultValue(class_exists('Symfony\Bundle\SecurityBundle\SecurityBundle'))->end();
        return $tree;
    }

    protected function addCmsOptions(ArrayNodeDefinition $arrayNode): void
    {
        $arrayNode->children()
            ->scalarNode('dashboard_template')->defaultValue('@Apie/dashboard.html.twig')->end()
            ->scalarNode('error_template')->defaultValue('@Apie/error.html.twig')->end();
    }

    protected function addApiOptions(ArrayNodeDefinition $arrayNode): void
    {
    }
}
