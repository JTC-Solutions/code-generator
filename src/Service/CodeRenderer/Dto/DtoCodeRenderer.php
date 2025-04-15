<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer\Dto;

use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\BaseRenderer;

class DtoCodeRenderer extends BaseRenderer
{
    public function renderCode(IRenderableConfiguration $configuration): string
    {
        $this->addDeclareStrictTypes();
        $this->addNamespace($configuration);
        $this->addUseStatements($configuration);
        $this->addClassName($configuration, true);
        $this->addExtendedClasses($configuration);
        $this->addImplementedInterfaces($configuration);

        $this->code .= "\n{\n    // TODO: Add properties\n}\n";

        return $this->code;
    }
}
