<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\OpenApiDoc;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\IConfiguration;

interface IOpenApiDocConfiguration extends IConfiguration
{
    public function render(): string;
}
