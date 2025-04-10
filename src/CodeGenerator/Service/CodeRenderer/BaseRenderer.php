<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;

class BaseRenderer
{
    public function __construct(
        protected readonly ControllerConfiguration $configuration, // TODO: change to interface
        protected string $code = '',
    ) {
    }

    protected function addNamespace(): void
    {
        $this->code .= "namespace {$this->configuration->namespace};\n\n";
    }

    protected function addDeclareStrictTypes(): void
    {
        $this->code = "<?php declare(strict_types = 1);\n\n";
    }

    protected function addUseStatements(): void
    {
        if ($this->configuration->useStatements !== []) {
            foreach ($this->configuration->useStatements as $use) {
                $this->code .= "use {$use};\n";
            }
            $this->code .= "\n";
        }
    }

    protected function addOpenApiDoc(): void
    {
        foreach ($this->configuration->openApiDocs as $doc) {
            $this->code .= $doc->render() . "\n"; // TODO: Maybe add open api render that will handle it
        }
    }

    protected function addClassName(): void
    {
        $this->code .= "class {$this->configuration->className}";
    }

    protected function addExtendedClasses(): void
    {
        if ($this->configuration->extends !== []) {
            $this->code .= ' extends ' . implode(', ', $this->configuration->extends);
        }
    }

    protected function addImplementedInterfaces(): void
    {
        if ($this->configuration->interfaces !== []) {
            $this->code .= ' implements ' . implode(', ', $this->configuration->interfaces);
        }
    }

    protected function addConstructor(): void
    {
        if ($this->configuration->constructorParams !== []) {
            $this->code .= "    public function __construct(\n";

            $paramStrings = [];
            foreach ($this->configuration->constructorParams as $param) {
                $paramStrings[] = "        {$param->argumentType} \${$param->argumentName}";
            }

            $this->code .= implode(",\n", $paramStrings) . "\n";
            $this->code .= "    ) {\n";

            if ($this->configuration->constructorBody !== null) {
                $this->code .= "        {$this->configuration->constructorBody}\n";
            }

            $this->code .= "    }\n\n";
        }
    }

    protected function addMethodAttributes(): void
    {
        if (
            $this->configuration->methodConfiguration === null
            || $this->configuration->methodConfiguration->attributes === []
        ) {
            return;
        }

        foreach ($this->configuration->methodConfiguration->attributes as $attribute) {
            $this->code .= '    ' . $attribute->render() . "\n";
        }
    }

    protected function addMethodName(): void
    {
        if (
            $this->configuration->methodConfiguration === null
        ) {
            return;
        }

        $this->code .= "    public function {$this->configuration->methodConfiguration->name}(\n";
    }

    protected function addMethodArguments(): void
    {
        if (
            $this->configuration->methodConfiguration === null
        ) {
            return;
        }

        $argStrings = [];

        foreach ($this->configuration->methodConfiguration->arguments as $arg) {
            $argStrings[] = "        {$arg->argumentType} \${$arg->argumentName}";
        }

        if ($argStrings !== []) {
            $this->code .= implode(",\n", $argStrings) . "\n";
        }

        $this->code .= "    ): {$this->configuration->methodConfiguration->returnType} {\n";
    }

    protected function addMethodBody(): void
    {
        if ($this->configuration->methodConfiguration === null) {
            return;
        }

        $methodLine = explode("\n", $this->configuration->methodConfiguration->methodBody);

        foreach ($methodLine as $line) {
            $this->code .= "        {$line}\n";
        }

        $this->code .= "    }\n";

        $this->code .= "}\n";
    }
}
