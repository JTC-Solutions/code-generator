<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\OpenApiDoc;

final readonly class OpenApiDocResponseConfiguration implements IOpenApiDocConfiguration
{
    public function __construct(
        public string $response,
        public string $description,
        public ?string $content = null, // TODO: change this to more dynamic
    ) {
    }

    public function getIdentifier(): string
    {
        return self::class . '_' . $this->response;
    }

    public function render(): string
    {
        $result = "#[OA\Response(\n";
        $result .= "    response: {$this->response},\n";
        $result .= "    description: '{$this->description}',\n";

        if ($this->content !== null) {
            $result .= "    content: {$this->content},\n";
        }

        $result .= ')]';

        return $result;
    }
}
