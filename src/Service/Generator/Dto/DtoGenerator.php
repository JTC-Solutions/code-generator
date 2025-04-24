<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Dto;

use Exception;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\Dto\DtoCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Dto\DtoConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\Dto\DtoClassWriter;
use ReflectionException;
use RuntimeException;

/**
 * Generates Data Transfer Object (DTO) classes based on entity properties.
 * Orchestrates the configuration, rendering, and writing of DTO classes.
 */
class DtoGenerator
{
    /**
     * @param DtoClassWriter $classWriter Service to write the generated DTO code to a file.
     * @param DtoConfigurator $configurator Service to configure the DTO structure based on an entity.
     * @param DtoCodeRenderer $codeRenderer Service to render the PHP code for the DTO from its configuration.
     */
    public function __construct(
        protected readonly DtoClassWriter $classWriter,
        protected readonly DtoConfigurator $configurator,
        protected readonly DtoCodeRenderer $codeRenderer,
        protected readonly string $prefix = '',
        protected readonly string $suffix = '',
    ) {
    }

    /**
     * Generates a DTO class file based on the target entity class.
     *
     * @param class-string $classFullyQualifiedClassName The fully qualified class name of the entity to base the DTO on.
     * @return class-string The fully qualified class name of the generated DTO.
     * @throws ConfigurationException If DTO configuration fails.
     * @throws RuntimeException If file writing fails.
     * @throws TemplateNotValidPhpCodeException If generated DTO code is invalid PHP.
     * @throws ReflectionException If reflection on the target class fails.
     * @throws Exception For other general errors during generation.
     */
    public function generate(
        string $classFullyQualifiedClassName,
    ): string {
        $configuration = $this->configurator->configure($classFullyQualifiedClassName, $this->prefix, $this->suffix);
        $code = $this->codeRenderer->renderCode($configuration);

        $dtoFullyQualifiedClassName = $configuration->getFullyQualifiedClassName();

        $this->classWriter->write($classFullyQualifiedClassName, $dtoFullyQualifiedClassName, $code);

        return $dtoFullyQualifiedClassName;
    }
}
