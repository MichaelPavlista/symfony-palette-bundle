<?php declare(strict_types=1);

namespace SymfonyPaletteBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class PaletteExtension
 * @package SymfonyPaletteBundle\DependencyInjection
 */
class PaletteExtension extends Extension
{
    /**
     * Loads extension config and register services.
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yml');

        $definition = $container->getDefinition('palette.palette');
        $definition->replaceArgument(1, $config);
    }
}
