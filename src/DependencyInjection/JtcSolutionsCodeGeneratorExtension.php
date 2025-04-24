<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JtcSolutionsCodeGeneratorExtension extends Extension
{
    protected const string ALIAS = 'jtc_solutions_code_generator';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // namespaces
        $container->setParameter('jtc_solutions_code_generator.global.controller_namespace_template', $config['global']['namespace']['controllerNamespaceTemplate']);
        $container->setParameter('jtc_solutions_code_generator.global.dto_namespace_template', $config['global']['namespace']['dtoNamespaceTemplate']);
        $container->setParameter('jtc_solutions_code_generator.global.service_namespace_template', $config['global']['namespace']['serviceNamespaceTemplate']);
        $container->setParameter('jtc_solutions_code_generator.global.repository_namespace_template', $config['global']['namespace']['repositoryNamespaceTemplate']);

        // project setting
        $container->setParameter('jtc_solutions_code_generator.global.project_dir', $config['global']['project']['projectDir']);
        $container->setParameter('jtc_solutions_code_generator.global.project_base_namespace', $config['global']['project']['projectBaseNamespace']);
        $container->setParameter('jtc_solutions_code_generator.global.entity_interface', $config['global']['project']['entityInterface']);
        $container->setParameter('jtc_solutions_code_generator.global.dto_entity_replacement', $config['global']['project']['dtoEntityReplacement']);
        $container->setParameter('jtc_solutions_code_generator.global.request_dto_interface', $config['global']['project']['requestDtoInterface']);
        $container->setParameter('jtc_solutions_code_generator.global.ignored_properties', $config['global']['project']['ignoredProperties'] ?? []);

        // open api
        $container->setParameter('jtc_solutions_code_generator.global.error_response_class', $config['global']['openApi']['errorResponseClass']);
        $container->setParameter('jtc_solutions_code_generator.global.pagination_class', $config['global']['openApi']['paginationClass']);

        foreach ($config['controllers'] as $controllerType => $controllerConfig) {
            $parentParamName = sprintf('jtc_solutions_code_generator.controllers.%s.parent', $controllerType);
            $container->setParameter($parentParamName, $controllerConfig['parent']);
        }
    }

    public function getAlias(): string
    {
        return static::ALIAS;
    }
}
