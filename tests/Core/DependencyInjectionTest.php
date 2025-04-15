<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Core;

use JtcSolutions\CodeGenerator\DependencyInjection\JtcSolutionsCodeGeneratorExtension;
use JtcSolutions\CodeGenerator\JtcSolutionsCodeGeneratorBundle;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpKernel\KernelInterface;

class DependencyInjectionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder(new ParameterBag());

        $extension = new JtcSolutionsCodeGeneratorExtension();
        $this->container->registerExtension($extension);

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
        self::assertTrue($this->container->hasExtension('jtc_solutions_code_generator'));
    }

    public function testServicesAreDefined(): void
    {
        self::markTestSkipped();
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.factory.method_attribute_configuration_factory'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.factory.open_api_doc_configuration_factory'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.builder.configuration.controller_configuration_builder'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.builder.configuration.method_configuration_builder'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.writer.controller_class_writer'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.writer.dto_class_writer'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.generator.controller.base_controller_generator'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.generator.dto.dto_generator'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.configurator.controller.base_controller_configurator'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.configurator.dto.dto_configurator'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.code_renderer.controller.controller_code_renderer'));
        self::assertTrue($this->container->has('jtc_solutions_code_generator.service.code_renderer.dto.dto_code_renderer'));
    }

    public function testParametersAreSet(): void
    {
        // Check if the parameters from the configuration are correctly set.
        self::assertTrue($this->container->hasParameter('jtc_solutions_code_generator.global.controller_namespace_template'));
        self::assertTrue($this->container->hasParameter('jtc_solutions_code_generator.global.dto_namespace_template'));

        // Check the values of the parameters.  This is crucial for verifying
        // that your Configuration.php and Extension.php are working together.
        self::assertSame(
            'App\\Controller\\Generated', // Use the value from the $config array in setUp()
            $this->container->getParameter('jtc_solutions_code_generator.global.controller_namespace_template'),
        );
        self::assertSame(
            'App\\Dto\\Generated', // Use the value from the $config array in setUp()
            $this->container->getParameter('jtc_solutions_code_generator.global.dto_namespace_template'),
        );
    }

    public function testServiceWiring(): void
    {
        self::markTestSkipped();
        // Test that a service can be retrieved from the container and that its
        // dependencies are correctly injected.  Pick a service that has
        // dependencies.  ContextProvider is a good choice because it has
        // parameters injected.
        $contextProvider = $this->container->get('jtc_solutions_code_generator.service.provider.context_provider');
        self::assertInstanceOf(ContextProvider::class, $contextProvider);

        // Check that the parameters were injected correctly.  This assumes that
        // ContextProvider has public properties or getters for these.  If not,
        // you'll need to adjust this to use whatever methods are appropriate
        // for your class.  This is *very* important.
        $refl = new ReflectionClass($contextProvider);

        $controllerNamespaceTemplateProperty = $refl->getProperty('controllerNamespaceTemplate');
        $controllerNamespaceTemplateProperty->setAccessible(true);
        self::assertSame(
            'App\\Controller\\Generated', //  Use the value from the $config array in setUp()
            $controllerNamespaceTemplateProperty->getValue($contextProvider),
        );

        $dtoNamespaceTemplateProperty = $refl->getProperty('dtoNamespaceTemplate');
        $dtoNamespaceTemplateProperty->setAccessible(true);
        self::assertSame(
            'App\\Dto\\Generated', // Use the value from the $config array in setUp()
            $dtoNamespaceTemplateProperty->getValue($contextProvider),
        );
    }

    public function testBundleRegistration(): void
    {
        // Test that the bundle can be registered and its extension is available.
        $kernel = $this->createMock(KernelInterface::class);
        $bundles = [
            'JtcSolutionsCodeGeneratorBundle' => new JtcSolutionsCodeGeneratorBundle(),
        ];

        $kernel->method('getBundles')->willReturn($bundles);

        $bundle = $bundles['JtcSolutionsCodeGeneratorBundle'];
        self::assertInstanceOf(JtcSolutionsCodeGeneratorBundle::class, $bundle);
        self::assertSame('JtcSolutionsCodeGeneratorBundle', $bundle->getName());
        self::assertInstanceOf(
            JtcSolutionsCodeGeneratorExtension::class,
            $bundle->getContainerExtension(),
        );
    }
}
