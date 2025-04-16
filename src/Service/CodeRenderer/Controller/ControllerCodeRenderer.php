<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use TypeError;

/**
 * Concrete implementation for rendering Controller PHP code.
 * Uses helper methods from BaseControllerRenderer and BaseRenderer to assemble the code string.
 */
class ControllerCodeRenderer extends BaseControllerRenderer implements IControllerCodeRenderer
{
    /**
     * Renders the complete Controller PHP code string from the configuration.
     * Orchestrates calls to helper methods in the correct order.
     *
     * @param ControllerConfiguration $configuration The controller configuration DTO.
     * @return string The generated PHP code for the controller class.
     * @throws TypeError If the provided configuration is not a ControllerConfiguration instance.
     */
    public function renderCode(IRenderableConfiguration $configuration): string
    {
        $this->addDeclareStrictTypes();
        $this->addNamespace($configuration);
        $this->addUseStatements($configuration);
        $this->addOpenApiDoc($configuration);
        $this->addClassName($configuration);
        $this->addExtendedClasses($configuration);
        $this->addImplementedInterfaces($configuration);

        $this->code .= "\n{\n"; // open class body

        $this->addConstructor($configuration);

        if ($configuration->methodConfiguration === null) {
            return $this->code;
        }

        $this->addMethodAttributes($configuration);
        $this->addMethodName($configuration);
        $this->addMethodArguments($configuration);
        $this->addMethodBody($configuration);

        return $this->code;
    }
}
