<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use Exception;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\CreateControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\CodeGenerator\Service\Writer\Controller\ControllerClassWriter;
use JtcSolutions\Core\Entity\IEntity;
use ReflectionException;
use RuntimeException;

/**
 * Generates a controller responsible for creating an entity.
 * This generator first generates the required Request DTO using DtoGenerator.
 */
class CreateControllerGenerator extends BaseControllerGenerator
{
    /**
     * @param ContextProvider $contextProvider Provides context like namespaces and paths, and holds the generated DTO FQCN.
     * @param CreateControllerConfigurator $configurator Configurator specific to the 'create' controller action.
     * @param ControllerClassWriter $classWriter Service to write the generated code to a file.
     * @param ControllerCodeRenderer $codeRenderer Service to render the PHP code from the configuration.
     */
    public function __construct(
        protected readonly ContextProvider $contextProvider,
        CreateControllerConfigurator $configurator,
        ControllerClassWriter $classWriter,
        ControllerCodeRenderer $codeRenderer,
    ) {
        parent::__construct($configurator, $classWriter, $codeRenderer);
    }

    /**
     * @param class-string<IEntity> $classFullyQualifiedClassName
     * @throws ConfigurationException If DTO configuration fails.
     * @throws RuntimeException If file writing fails.
     * @throws TemplateNotValidPhpCodeException If generated DTO code is invalid PHP.
     * @throws ReflectionException If reflection on the target class fails.
     * @throws Exception For other general errors during generation.
     */
    public function generate(string $classFullyQualifiedClassName): void
    {
        parent::generate($classFullyQualifiedClassName);
    }
}
