<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;

interface IControllerCodeRenderer
{
    public function __construct(
        ControllerConfiguration $configuration,
        string $code = '',
    );

    public function generateCode(): string;
}
