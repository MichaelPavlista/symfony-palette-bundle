<?php declare(strict_types=1);

namespace SymfonyPaletteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package SymfonyPaletteBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Configure palette config builder.
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('palette');

        $rootNode
            ->children()
                ->scalarNode('path')->isRequired()->end()
            ->end()
            ->children()
                ->scalarNode('url')->isRequired()->end()
            ->end()
            ->children()
                ->scalarNode('basePath')->isRequired()->end()
            ->end()
            ->children()
                ->scalarNode('signingKey')->isRequired()->end()
            ->end()
            ->children()
                ->scalarNode('fallbackImage')->end()
            ->end()
            ->children()
                ->scalarNode('websiteUrl')->end()
            ->end()
            ->children()
                ->scalarNode('defaultQuality')->end()
            ->end()
            ->children()
                ->arrayNode('templates')->useAttributeAsKey('name')->prototype('scalar')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
