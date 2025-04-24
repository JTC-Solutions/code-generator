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
}
