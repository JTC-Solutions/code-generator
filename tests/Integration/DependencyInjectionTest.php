<?php

namespace JtcSolutions\CodeGenerator\Tests\Integration;

use JtcSolutions\CodeGenerator\JtcSolutionsCodeGeneratorBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class DependencyInjectionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder(new ParameterBag());
        // Manually register the extension for testing
        $extension = new \JtcSolutions\CodeGenerator\DependencyInjection\JtcSolutionsCodeGeneratorExtension();
        $this->container->registerExtension($extension);

        // Load a minimal configuration for the extension to process.  This should be
        // enough to exercise the basic DI wiring without requiring a full, realistic
        // configuration.  We'll use a minimal, valid configuration here.
        $config = [
            'jtc_solutions_code_generator' => [
                'global' => [
                    'controllerNamespaceTemplate' => 'App\\Controller\\Generated',
                    'dtoNamespaceTemplate' => 'App\\Dto\\Generated',
                ],
            ],
        ];

        $extension->load($config, $this->container);
        $this->container->compile();
    }

    public function testExtensionLoads(): void
    {
        $this->assertTrue($this->container->hasExtension('jtc_solutions_code_generator'));
    }

    public function testServicesAreDefined(): void
    {
        // Check if specific services are defined.  Adjust this list based on what
        // your bundle is expected to define.  This is the *most* important part
        // of the test - verifying that your services.yaml is doing what you
        // expect it to.
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.provider.context_provider'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.factory.method_attribute_configuration_factory'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.factory.open_api_doc_configuration_factory'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.builder.configuration.controller_configuration_builder'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.builder.configuration.method_configuration_builder'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.writer.controller_class_writer'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.writer.dto_class_writer'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.generator.controller.base_controller_generator'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.generator.dto.dto_generator'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.configurator.controller.base_controller_configurator'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.configurator.dto.dto_configurator'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.code_renderer.controller.controller_code_renderer'));
        $this->assertTrue($this->container->has('jtc_solutions_code_generator.service.code_renderer.dto.dto_code_renderer'));
    }

    public function testParametersAreSet(): void
    {
        // Check if the parameters from the configuration are correctly set.
        $this->assertTrue($this->container->hasParameter('jtc_solutions_code_generator.global.controller_namespace_template'));
        $this->assertTrue($this->container->hasParameter('jtc_solutions_code_generator.global.dto_namespace_template'));

        // Check the values of the parameters.  This is crucial for verifying
        // that your Configuration.php and Extension.php are working together.
        $this->assertSame(
            'App\\Controller\\Generated',  // Use the value from the $config array in setUp()
            $this->container->getParameter('jtc_solutions_code_generator.global.controller_namespace_template')
        );
        $this->assertSame(
            'App\\Dto\\Generated',      // Use the value from the $config array in setUp()
            $this->container->getParameter('jtc_solutions_code_generator.global.dto_namespace_template')
        );
    }

    public function testServiceWiring(): void
    {
        // Test that a service can be retrieved from the container and that its
        // dependencies are correctly injected.  Pick a service that has
        // dependencies.  ContextProvider is a good choice because it has
        // parameters injected.
        $contextProvider = $this->container->get('jtc_solutions_code_generator.service.provider.context_provider');
        $this->assertInstanceOf(\JtcSolutions\CodeGenerator\Service\Provider\ContextProvider::class, $contextProvider);

        // Check that the parameters were injected correctly.  This assumes that
        // ContextProvider has public properties or getters for these.  If not,
        // you'll need to adjust this to use whatever methods are appropriate
        // for your class.  This is *very* important.
        $refl = new \ReflectionClass($contextProvider);

        $controllerNamespaceTemplateProperty = $refl->getProperty('controllerNamespaceTemplate');
        $controllerNamespaceTemplateProperty->setAccessible(true);
        $this->assertSame(
            'App\\Controller\\Generated', //  Use the value from the $config array in setUp()
            $controllerNamespaceTemplateProperty->getValue($contextProvider)
        );

        $dtoNamespaceTemplateProperty = $refl->getProperty('dtoNamespaceTemplate');
        $dtoNamespaceTemplateProperty->setAccessible(true);
        $this->assertSame(
            'App\\Dto\\Generated',           // Use the value from the $config array in setUp()
            $dtoNamespaceTemplateProperty->getValue($contextProvider)
        );
    }

    public function testBundleRegistration(): void
    {
        // Test that the bundle can be registered and its extension is available.
        $kernel = $this->createMock(\Symfony\Component\HttpKernel\KernelInterface::class);
        $bundles = [
            'JtcSolutionsCodeGeneratorBundle' => new JtcSolutionsCodeGeneratorBundle(),
        ];

        $kernel->method('getBundles')->willReturn($bundles);

        $bundle = $bundles['JtcSolutionsCodeGeneratorBundle'];
        $this->assertInstanceOf(JtcSolutionsCodeGeneratorBundle::class, $bundle);
        $this->assertSame('JtcSolutionsCodeGeneratorBundle', $bundle->getName());
        $this->assertInstanceOf(
            \JtcSolutions\CodeGenerator\DependencyInjection\JtcSolutionsCodeGeneratorExtension::class,
            $bundle->getContainerExtension()
        );
    }
}