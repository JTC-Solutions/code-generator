<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer;

use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;

abstract class BaseRenderer
{
    protected string $code = '';

    abstract protected function renderCode(IRenderableConfiguration $configuration): string;

    protected function addNamespace(IRenderableConfiguration $configuration): void
    {
        $this->code .= "namespace {$configuration->getNamespace()};\n\n";
    }

    protected function addDeclareStrictTypes(): void
    {
        $this->code = "<?php declare(strict_types = 1);\n\n";
    }

    protected function addUseStatements(IRenderableConfiguration $configuration): void
    {
        /** @var array<string, UseStatementConfiguration> $useStatements */
        $useStatements = $configuration->getUseStatements();

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
        IRenderableConfiguration $configuration,
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

        $this->code .= "class {$configuration->getClassName()}";
    }

    protected function addExtendedClasses(IRenderableConfiguration $configuration): void
    {
        if ($configuration->getExtends() !== []) {
            $this->code .= ' extends ' . implode(', ', $configuration->getExtends());
        }
    }

    protected function addImplementedInterfaces(IRenderableConfiguration $configuration): void
    {
        if ($configuration->getInterfaces() !== []) {
            $this->code .= ' implements ' . implode(', ', $configuration->getInterfaces());
        }
    }
}
