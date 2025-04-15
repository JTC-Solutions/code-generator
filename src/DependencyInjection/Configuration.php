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
                            ->info('Template for controller namespaces. Use {domain} or {entity} for Domain and Entity.')
                            ->example('App\{entity}\App\Api\{entity}}')
                        ->end()
                        ->scalarNode('dtoNamespaceTemplate')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Template for DTO namespaces. Use {domain} or {entity} for Domain and Entity.')
                            ->example('App\{entity}\Domain\Dto\{domain}}')
                        ->end()
                        ->scalarNode('errorResponseClass')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('FQCN of class that will be used as error DTO.')
                            ->example('App\Shared\Dto\ErrorResponse')
                    ->end()
                ->end() // global
            ->end();

        return $treeBuilder;
    }
}
