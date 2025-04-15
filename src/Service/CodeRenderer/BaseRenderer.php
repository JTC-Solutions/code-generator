<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer;

use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;

abstract class BaseRenderer
{
    protected string $code = '';

    abstract protected function renderCode(IRenderableConfiguration $configuration): string;

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
        /** @var array<string, UseStatementConfiguration> $useStatements */
        $useStatements = $this->configuration->getUseStatements();

        if ($useStatements !== []) {
            // Array should be sorted by FQCN key in the builder before rendering
            foreach ($useStatements as $useStatementDto) {
                // Check if an alias exists for this use statement
                if ($useStatementDto->alias !== null) {
                    // Format with 'as Alias'
                    $this->code .= sprintf('use %s as %s;', $useStatementDto->fqcn, $useStatementDto->alias) . "\n";
                } else {
                    // Format without alias
                    $this->code .= sprintf('use %s;', $useStatementDto->fqcn) . "\n";
                }
            }
            $this->code .= "\n"; // Add blank line after use statements
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
