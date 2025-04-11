<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\IConfiguration;

interface IOpenApiDocConfiguration extends IConfiguration
{
    public function render(): string;
}
