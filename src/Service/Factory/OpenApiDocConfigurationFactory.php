<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Factory;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc\OpenApiDocResponseConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc\OpenApiDocTagConfiguration;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;

class OpenApiDocConfigurationFactory
{
    protected const string RESPONSE_MODEL_TEMPLATE = 'new Model(type: %s::class, groups: [\'%s\'])';

    protected const string RESPONSE_JSON_CONTENT_TEMPLATE = 'new OA\JsonContent(
        properties: [
            new OA\Property(
                property: \'data\',
                type: \'array\',
                items: new OA\Items(ref: new Model(type: %s::class, groups: [\'%s\'])),
            ),
            new OA\Property(
                property: \'metadata\',
                ref: new Model(type: Pagination::class, groups: [\'reference\']),
            ),
        ],
        type: \'object\',
    )';

    /**
     * @param class-string $name
     */
    public function createTag(string $name): OpenApiDocTagConfiguration
    {
        $className = FQCNHelper::transformFQCNToShortClassName($name);
        $classNameSnakeCase = StringUtils::toSnakeCase($className);

        return new OpenApiDocTagConfiguration($classNameSnakeCase);
    }

    /**
     * @param string[] $groups
     * @param class-string|string $type
     */
    public function createModelResponse(
        string $responseCode,
        string $description,
        string $type,
        array $groups,
    ): OpenApiDocResponseConfiguration {
        $model = FQCNHelper::transformFQCNToShortClassName($type, false);

        return new OpenApiDocResponseConfiguration(
            response: $responseCode,
            description: $description,
            content: sprintf(self::RESPONSE_MODEL_TEMPLATE, $model, implode(', ', $groups)),
        );
    }

    public function createEmptyResponse(
        string $responseCode,
        string $description,
    ): OpenApiDocResponseConfiguration {
        return new OpenApiDocResponseConfiguration(
            response: $responseCode,
            description: $description,
        );
    }

    /**
     * @param string[] $groups
     * @param class-string|string $type
     */
    public function createJsonContentResponse(
        string $responseCode,
        string $description,
        string $type,
        array $groups,
    ): OpenApiDocResponseConfiguration {
        $model = FQCNHelper::transformFQCNToShortClassName($type, false);

        return new OpenApiDocResponseConfiguration(
            response: $responseCode,
            description: $description,
            content: sprintf(self::RESPONSE_JSON_CONTENT_TEMPLATE, $model, implode(', ', $groups)),
        );
    }
}
