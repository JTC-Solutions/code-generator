<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\ICodeRenderer;

/**
 * Interface specific for rendering Controller classes.
 * Extends the base ICodeRenderer and specifies ControllerConfiguration type.
 */
interface IControllerCodeRenderer extends ICodeRenderer
{
    /**
     * Renders the Controller PHP code based on the provided ControllerConfiguration.
     *
     * @param ControllerConfiguration $configuration The configuration object holding the controller structure.
     * @return string The generated Controller PHP code as a string.
     */
    public function renderCode(IRenderableConfiguration $configuration): string;
}
