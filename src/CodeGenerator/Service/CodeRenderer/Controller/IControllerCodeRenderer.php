<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;

interface IControllerCodeRenderer
{
    public function __construct(
        ControllerConfiguration $configuration,
        string $code = '',
    );

    public function renderCode(): string;
}
