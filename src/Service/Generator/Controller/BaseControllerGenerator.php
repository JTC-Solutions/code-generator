<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use Exception;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\IControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\IControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\Controller\IControllerClassWriter;
use JtcSolutions\Core\Entity\IEntity;
use ReflectionException;
use RuntimeException;

/**
 * Base class for controller generators.
 * Orchestrates the configuration, rendering, and writing of controller classes.
 */
abstract class BaseControllerGenerator
{
    /**
     * @param IControllerConfigurator $configurator Service to configure the controller structure.
     * @param IControllerClassWriter $classWriter Service to write the generated code to a file.
     * @param IControllerCodeRenderer $codeRenderer Service to render the PHP code from the configuration.
     */
    public function __construct(
        protected readonly IControllerConfigurator $configurator,
        protected readonly IControllerClassWriter $classWriter,
        protected readonly IControllerCodeRenderer $codeRenderer,
    ) {
    }

    /**
     * Generates the controller class file based on the target entity class.
     *
     * @param class-string<IEntity> $classFullyQualifiedClassName The fully qualified class name of the target entity.
     * @throws ConfigurationException If configuration fails (e.g., duplicate items).
     * @throws RuntimeException If file writing fails.
     * @throws TemplateNotValidPhpCodeException If generated code is invalid PHP.
     * @throws ReflectionException If reflection on the target class fails within configurators/mappers.
     * @throws Exception For other general errors during generation (e.g., FQCN parsing).
     */
    public function generate(string $classFullyQualifiedClassName): void
    {
        $configuration = $this->configurator->configure($classFullyQualifiedClassName);
        $code = $this->codeRenderer->renderCode($configuration);

        $this->classWriter->write($classFullyQualifiedClassName, $configuration->getFullyQualifiedClassName(), $code);
    }
}
