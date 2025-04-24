<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer\Dto;

use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\BaseRenderer;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\ICodeRenderer;
use TypeError;

/**
 * Concrete implementation for rendering Data Transfer Object (DTO) PHP code.
 * Uses helper methods from BaseRenderer to assemble the code string.
 * Generates DTOs with promoted constructor properties.
 */
class DtoCodeRenderer extends BaseRenderer implements ICodeRenderer
{
    /**
     * Renders the complete DTO PHP code string from the configuration.
     * Orchestrates calls to helper methods in the correct order.
     * DTOs are generated as readonly classes with promoted constructor properties.
     *
     * @param DtoConfiguration $configuration The DTO configuration DTO.
     * @return string The generated PHP code for the DTO class.
     * @throws TypeError If the provided configuration is not a DtoConfiguration instance.
     */
    public function renderCode(IRenderableConfiguration $configuration): string
    {
        $this->addDeclareStrictTypes();
        $this->addNamespace($configuration);
        $this->addUseStatements($configuration);
        $this->addClassName($configuration, true);
        $this->addExtendedClasses($configuration);
        $this->addImplementedInterfaces($configuration);

        $this->code .= "\n{\n";

        $this->addConstructor($configuration);

        $this->code .= "}\n";

        return $this->code;
    }

    /**
     * Adds the constructor with promoted properties based on the DTO configuration.
     *
     * @param DtoConfiguration $configuration The DTO configuration containing properties.
     */
    protected function addConstructor(DtoConfiguration $configuration): void
    {
        $this->code .= "    public function __construct(\n";

        foreach ($configuration->getProperties() as $property) {
            $this->code .= sprintf("        public %s \$%s,\n", $property->propertyType, $property->propertyName);
        }

        $this->code .= "    ) {}\n";
    }
}
