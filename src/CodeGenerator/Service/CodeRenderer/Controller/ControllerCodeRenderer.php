<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\Controller;

class ControllerCodeRenderer extends BaseControllerRenderer implements IControllerCodeRenderer
{
    public function renderCode(): string
    {
        $this->addDeclareStrictTypes();
        $this->addNamespace();
        $this->addUseStatements();
        $this->addOpenApiDoc();
        $this->addClassName();
        $this->addExtendedClasses();
        $this->addImplementedInterfaces();

        $this->code .= "\n{\n"; // open class body

        $this->addConstructor();

        if ($this->configuration->methodConfiguration === null) {
            return $this->code;
        }

        $this->addMethodAttributes();
        $this->addMethodName();
        $this->addMethodArguments();
        $this->addMethodBody();

        return $this->code;
    }
}
