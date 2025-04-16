<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer;

use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;

/**
 * Interface for code rendering services.
 * Defines the contract for rendering PHP code from a configuration object.
 * Note: The specific type of configuration is usually defined in extending interfaces.
 */
interface ICodeRenderer
{
    /**
     * Renders the PHP code based on the provided configuration.
     *
     * @param IRenderableConfiguration $configuration The configuration object holding the structure to render.
     * @return string The generated PHP code as a string.
     */
    public function renderCode(IRenderableConfiguration $configuration): string;
}
