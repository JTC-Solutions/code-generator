<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\Dto;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\BaseRenderer;

/** @property DtoConfiguration $configuration */
class DtoCodeRenderer extends BaseRenderer
{
    public function renderCode(): string
    {
        $this->addDeclareStrictTypes();
        $this->addNamespace();
        $this->addUseStatements();
        $this->addClassName(true);
        $this->addExtendedClasses();
        $this->addImplementedInterfaces();

        $this->code .= "\n{\n    // TODO: Add properties\n}\n";

        return $this->code;
    }
}
