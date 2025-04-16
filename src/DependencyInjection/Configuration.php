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
                        ->arrayNode('namespace')
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
                            ->end()
                        ->end() // namespace
                        ->arrayNode('project')
                            ->children()
                                ->scalarNode('projectDir')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->info('Default project dir.')
                                    ->example('project or src')
                                ->end()
                                ->scalarNode('projectBaseNamespace')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->info('Project base namespace that is used also for path and storing generated files.')
                                    ->example('App')
                                ->end()
                                ->scalarNode('entityInterface')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->info('Interface that all ORM or ODM entities share')
                                    ->example('App\\Shared\\Domain\\Entity\\IEntity')
                                ->end()
                                ->scalarNode('dtoEntityReplacement')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->info('Class that replaces entities in dto generations')
                                    ->example('App\\Shared\\Domain\\Dto\\EntityId')
                                ->end()
                                ->scalarNode('requestDtoInterface')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->info('Interface that will be given to entity request DTO')
                                    ->example('App\\Shared\\Domain\\Dto\\CRUD\\IEntityRequestBody')
                                ->end()
                                ->arrayNode('ignoredProperties')
                                    ->scalarPrototype()->end()
                                    ->info('Array of name for properties that should be ignored')
                                    ->end()
                            ->end()
                        ->end() // project
                        ->arrayNode('openApi')
                            ->children()
                                ->scalarNode('errorResponseClass')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->info('FQCN of class that will be used as error DTO.')
                                    ->example('App\Shared\Dto\ErrorResponse')
                                ->end()
                                ->scalarNode('paginationClass')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->info('FQCN of class that will be used as pagination DTO.')
                                    ->example('App\Shared\Dto\Pagination')
                                ->end()
                            ->end()
                        ->end() // openApi
                    ->end()
                ->end() // global
            ->end();

        return $treeBuilder;
    }
}
