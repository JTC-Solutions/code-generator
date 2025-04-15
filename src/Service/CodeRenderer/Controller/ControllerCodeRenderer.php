<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;

class ControllerCodeRenderer extends BaseControllerRenderer implements IControllerCodeRenderer
{
    /**
     * @param ControllerConfiguration $configuration
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
