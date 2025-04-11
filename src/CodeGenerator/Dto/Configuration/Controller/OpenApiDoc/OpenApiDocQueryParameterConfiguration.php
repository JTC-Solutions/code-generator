<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc;

final readonly class OpenApiDocQueryParameterConfiguration implements IOpenApiDocConfiguration
{
    public function __construct(
        public string $name,
        public string $description,
        public string $schema, // TODO: Change this to more dynamic
    ) {
    }

    public function getIdentifier(): string
    {
        return self::class . '_' . $this->name;
    }

    public function render(): string
    {
        return "#[OA\QueryParameter(name: '{$this->name}', description: '{$this->description}', schema: {$this->schema})]";
    }
}
