<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer\Dto;

use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\BaseRenderer;

class DtoCodeRenderer extends BaseRenderer
{
    /**
     * @param DtoConfiguration $configuration
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

        return $this->code;
    }

    protected function addConstructor(DtoConfiguration $configuration): void
    {
        $this->code .= "    public function __construct(\n";

        foreach ($configuration->getProperties() as $property) {
            $this->code .= sprintf("        public readonly %s \$%s,\n", $property->propertyType, $property->propertyName);
        }

        $this->code .= "    ) {}\n";
        $this->code .= "}\n";
    }
}
