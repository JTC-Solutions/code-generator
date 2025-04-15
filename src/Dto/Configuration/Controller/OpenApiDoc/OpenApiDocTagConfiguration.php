<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc;

final readonly class OpenApiDocTagConfiguration implements IOpenApiDocConfiguration
{
    public function __construct(
        public string $name,
    ) {
    }

    public function getIdentifier(): string
    {
        return self::class . '_' . $this->name;
    }

    public function render(): string
    {
        return "#[OA\Tag(name: '{$this->name}')]";
    }
}
