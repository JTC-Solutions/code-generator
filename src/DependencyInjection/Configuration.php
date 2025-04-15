<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jtc_solutions_code_generator');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('global')
                    ->children()
                        ->scalarNode('controllerNamespaceTemplate')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Template for controller namespaces. Use %s for Domain and Entity.')
                            ->example('App\%s\App\Api\%s')
                        ->end()
                        ->scalarNode('dtoNamespaceTemplate')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Template for DTO namespaces. Use %s for Domain and Entity.')
                            ->example('App\%s\Domain\Dto\%s')
                        ->end()
                    ->end()
                ->end() // global
            ->end();

        return $treeBuilder;
    }
}
