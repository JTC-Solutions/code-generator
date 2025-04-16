<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer;

use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;

/**
 * Abstract base class for code renderers.
 * Provides common helper methods for building PHP code strings.
 */
abstract class BaseRenderer
{
    /**
     * @var string Holds the generated code string during the rendering process.
     */
    protected string $code = '';

    /**
     * Abstract method to perform the actual rendering logic.
     * Subclasses must implement this to assemble the code using helper methods.
     *
     * @param IRenderableConfiguration $configuration The configuration object to render.
     * @return string The fully rendered PHP code string.
     */
    abstract protected function renderCode(IRenderableConfiguration $configuration): string;

    /**
     * Adds the namespace declaration to the code string.
     * Example: namespace App\MyNamespace;
     *
     * @param IRenderableConfiguration $configuration Configuration containing the namespace.
     */
    protected function addNamespace(IRenderableConfiguration $configuration): void
    {
        $this->code .= "namespace {$configuration->getNamespace()};\n\n";
    }

    /**
     * Adds the PHP opening tag and strict types declaration.
     * Example: <?php declare(strict_types = 1);
     */
    protected function addDeclareStrictTypes(): void
    {
        $this->code = "<?php declare(strict_types = 1);\n\n";
    }

    /**
     * Adds use statements to the code string based on the configuration.
     * Handles aliases correctly.
     * Example: use App\MyClass;
     * Example: use App\AnotherClass as AC;
     *
     * @param IRenderableConfiguration $configuration Configuration containing use statements.
     */
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

    /**
     * Adds the class keyword and name, optionally with 'readonly', 'final', or 'abstract' modifiers.
     * Example: readonly class MyClass
     * Example: final class MyClass
     * Example: abstract class MyBaseClass
     * Example: class MyClass
     *
     * @param IRenderableConfiguration $configuration Configuration containing the class name.
     * @param bool $readonly Add the 'readonly' modifier.
     * @param bool $final Add the 'final' modifier.
     * @param bool $abstract Add the 'abstract' modifier.
     */
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

    /**
     * Adds the 'extends' part of the class definition if parent classes are specified.
     * Example: extends BaseClass
     * Example: extends BaseClass, AnotherBase
     *
     * @param IRenderableConfiguration $configuration Configuration containing extended classes.
     */
    protected function addExtendedClasses(IRenderableConfiguration $configuration): void
    {
        if ($configuration->getExtends() !== []) {
            $this->code .= ' extends ' . implode(', ', $configuration->getExtends());
        }
    }

    /**
     * Adds the 'implements' part of the class definition if interfaces are specified.
     * Example: implements MyInterface
     * Example: implements MyInterface, AnotherInterface
     *
     * @param IRenderableConfiguration $configuration Configuration containing implemented interfaces.
     */
    protected function addImplementedInterfaces(IRenderableConfiguration $configuration): void
    {
        if ($configuration->getInterfaces() !== []) {
            $this->code .= ' implements ' . implode(', ', $configuration->getInterfaces());
        }
    }
}
