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

        $container->setParameter('jtc_solutions_code_generator.global.controller_namespace_template', $config['global']['controllerNamespaceTemplate']);
        $container->setParameter('jtc_solutions_code_generator.global.dto_namespace_template', $config['global']['dtoNamespaceTemplate']);
        $container->setParameter('jtc_solutions_code_generator.global.project_dir', $config['global']['projectDir']);
        $container->setParameter('jtc_solutions_code_generator.global.project_base_namespace', $config['global']['projectBaseNamespace']);
        $container->setParameter('jtc_solutions_code_generator.global.error_response_class', $config['global']['errorResponseClass']);
    }

    public function getAlias(): string
    {
        return static::ALIAS;
    }
}
