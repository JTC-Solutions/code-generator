<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\BaseRenderer;

/**
 * Abstract base class specifically for rendering Controller classes.
 * Provides helper methods for controller-specific elements like OpenAPI docs,
 * constructors, and methods.
 */
abstract class BaseControllerRenderer extends BaseRenderer
{
    /**
     * Adds OpenAPI attribute annotations (e.g., #[OA\Tag], #[OA\Response]) to the code string.
     *
     * @param ControllerConfiguration $configuration Configuration containing OpenAPI doc configurations.
     */
    protected function addOpenApiDoc(ControllerConfiguration $configuration): void
    {
        foreach ($configuration->openApiDocs as $doc) {
            $this->code .= $doc->render() . "\n"; // TODO: Maybe add open api render that will handle it
        }
    }

    /**
     * Adds the constructor method to the code string if constructor parameters are defined.
     * Handles promoted properties syntax and optional parent::__construct call.
     *
     * @param ControllerConfiguration $configuration Configuration containing constructor parameters and settings.
     */
    protected function addConstructor(ControllerConfiguration $configuration): void
    {
        if ($configuration->constructorParams !== []) {
            $this->code .= "    public function __construct(\n";

            $paramStrings = [];
            $paramNames = [];

            foreach ($configuration->constructorParams as $param) {
                $paramStrings[] = "        {$param->argumentType} \${$param->argumentName}";
                $paramNames[] = "\${$param->argumentName}";
            }

            $this->code .= implode(",\n", $paramStrings) . "\n";
            $this->code .= "    ) {\n";

            if ($configuration->callParent) {
                $this->code .= '        parent::__construct(' . implode(', ', $paramNames) . ");\n";
            }

            if ($configuration->constructorBody !== null) {
                $this->code .= "        {$configuration->constructorBody}\n";
            }

            $this->code .= "    }\n\n";
        }
    }

    /**
     * Adds method-level attributes (e.g., #[Route]) to the code string.
     *
     * @param ControllerConfiguration $configuration Configuration potentially containing method attributes.
     */
    protected function addMethodAttributes(ControllerConfiguration $configuration): void
    {
        if (
            $configuration->methodConfiguration === null
            || $configuration->methodConfiguration->attributes === []
        ) {
            return;
        }

        foreach ($configuration->methodConfiguration->attributes as $attribute) {
            $this->code .= '    ' . $attribute->render() . "\n";
        }
    }

    /**
     * Adds the method signature start (e.g., "public function myMethod(").
     *
     * @param ControllerConfiguration $configuration Configuration containing the method name.
     */
    protected function addMethodName(ControllerConfiguration $configuration): void
    {
        if (
            $configuration->methodConfiguration === null
        ) {
            return;
        }

        $this->code .= "    public function {$configuration->methodConfiguration->name}(\n";
    }

    /**
     * Adds the method arguments and the closing parenthesis of the signature, plus the return type.
     * Example: (Request $request, UuidInterface $id): JsonResponse {
     *
     * @param ControllerConfiguration $configuration Configuration containing method arguments and return type.
     */
    protected function addMethodArguments(ControllerConfiguration $configuration): void
    {
        if (
            $configuration->methodConfiguration === null
        ) {
            return;
        }

        $argStrings = [];

        foreach ($configuration->methodConfiguration->arguments as $arg) {
            $argStrings[] = "        {$arg->argumentType} \${$arg->argumentName}";
        }

        if ($argStrings !== []) {
            $this->code .= implode(",\n", $argStrings) . "\n";
        }

        $this->code .= "    ): {$configuration->methodConfiguration->returnType} {\n";
    }

    /**
     * Adds the method body content, correctly indented, and the closing brace for the method and class.
     *
     * @param ControllerConfiguration $configuration Configuration containing the method body.
     */
    protected function addMethodBody(ControllerConfiguration $configuration): void
    {
        if ($configuration->methodConfiguration === null) {
            return;
        }

        $methodLine = explode("\n", $configuration->methodConfiguration->methodBody);

        foreach ($methodLine as $line) {
            $this->code .= "        {$line}\n";
        }

        $this->code .= "    }\n";

        $this->code .= "}\n";
    }
}
