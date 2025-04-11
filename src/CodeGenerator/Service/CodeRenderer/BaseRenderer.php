<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\IRenderableConfiguration;

class BaseRenderer
{
    public function __construct(
        protected IRenderableConfiguration $configuration,
        protected string $code = '',
    ) {
    }

    protected function addNamespace(): void
    {
        $this->code .= "namespace {$this->configuration->getNamespace()};\n\n";
    }

    protected function addDeclareStrictTypes(): void
    {
        $this->code = "<?php declare(strict_types = 1);\n\n";
    }

    protected function addUseStatements(): void
    {
        if ($this->configuration->getUseStatements() !== []) {
            foreach ($this->configuration->getUseStatements() as $use) {
                $this->code .= "use {$use};\n";
            }
            $this->code .= "\n";
        }
    }

    protected function addClassName(
        bool $readonly = false,
        bool $final = false,
        bool $abstract = false,
    ): void {
        if ($final) {
            $this->code .= 'final ';
        }

        if ($abstract) {
            $this->code .= 'abstract ';
        }

        if ($readonly) {
            $this->code .= 'readonly ';
        }

        $this->code .= "class {$this->configuration->getClassName()}";
    }

    protected function addExtendedClasses(): void
    {
        if ($this->configuration->getExtends() !== []) {
            $this->code .= ' extends ' . implode(', ', $this->configuration->getExtends());
        }
    }

    protected function addImplementedInterfaces(): void
    {
        if ($this->configuration->getInterfaces() !== []) {
            $this->code .= ' implements ' . implode(', ', $this->configuration->getInterfaces());
        }
    }
}
