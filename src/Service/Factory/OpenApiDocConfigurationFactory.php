<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Factory;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc\OpenApiDocResponseConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc\OpenApiDocTagConfiguration;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;

/**
 * Factory for creating OpenAPI documentation configuration objects (Tags, Responses).
 * Helps generate standard OpenAPI attribute structures.
 */
class OpenApiDocConfigurationFactory
{
    /**
     * @const string Template for generating the content of an #[OA\Response] using #[Model].
     */
    protected const string RESPONSE_MODEL_TEMPLATE = 'new Model(type: %s::class, groups: [\'%s\'])';

    /**
     * @const string Template for generating the content of an #[OA\Response] using #[OA\JsonContent] for paginated lists.
     */
    protected const string RESPONSE_JSON_CONTENT_TEMPLATE = 'new OA\JsonContent(
        properties: [
            new OA\Property(
                property: \'data\',
                type: \'array\',
                items: new OA\Items(ref: new Model(type: %s::class, groups: [\'%s\'])),
            ),
            new OA\Property(
                property: \'metadata\',
                ref: new Model(type: %s::class, groups: [\'reference\']),
            ),
        ],
        type: \'object\',
    )';

    /**
     * Creates an OpenAPI Tag configuration based on the entity class name.
     * The tag name will be the snake_case version of the entity's short name.
     *
     * @param class-string $name The fully qualified class name of the entity.
     * @return OpenApiDocTagConfiguration The configured tag object.
     * @throws Exception If FQCN parsing or string conversion fails.
     */
    public function createTag(string $name): OpenApiDocTagConfiguration
    {
        $className = FQCNHelper::transformFQCNToShortClassName($name);
        $classNameSnakeCase = StringUtils::toSnakeCase($className);

        return new OpenApiDocTagConfiguration($classNameSnakeCase);
    }

    /**
     * Creates an OpenAPI Response configuration using Nelmio's Model attribute for content.
     *
     * @param string $responseCode The HTTP response code (e.g., 'Response::HTTP_OK', '404').
     * @param string $description The description for this response.
     * @param class-string|string $type The fully qualified class name of the model used in the response body.
     * @param string[] $groups Serialization groups to use for the response model.
     * @return OpenApiDocResponseConfiguration The configured response object.
     * @throws Exception If FQCN parsing fails.
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

    /**
     * Creates an OpenAPI Response configuration with no content (e.g., for 204 No Content).
     *
     * @param string $responseCode The HTTP response code (e.g., 'Response::HTTP_NO_CONTENT').
     * @param string $description The description for this response.
     * @return OpenApiDocResponseConfiguration The configured response object.
     */
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
     * Creates an OpenAPI Response configuration using OA\JsonContent for paginated list responses.
     * Defines 'data' (array of items) and 'metadata' (pagination info) properties.
     *
     * @param string $responseCode The HTTP response code (e.g., 'Response::HTTP_OK').
     * @param string $description The description for this response.
     * @param class-string|string $type The FQCN of the model used for items in the 'data' array.
     * @param string[] $groups Serialization groups for the items in the 'data' array.
     * @param class-string $paginationFullyQualifiedClassName The FQCN of the pagination metadata DTO.
     * @return OpenApiDocResponseConfiguration The configured response object.
     * @throws Exception If FQCN parsing fails.
     */
    public function createJsonContentResponse(
        string $responseCode,
        string $description,
        string $type,
        array $groups,
        string $paginationFullyQualifiedClassName,
    ): OpenApiDocResponseConfiguration {
        $model = FQCNHelper::transformFQCNToShortClassName($type);

        $paginationClassName = FQCNHelper::transformFQCNToShortClassName($paginationFullyQualifiedClassName);

        return new OpenApiDocResponseConfiguration(
            response: $responseCode,
            description: $description,
            content: sprintf(
                self::RESPONSE_JSON_CONTENT_TEMPLATE,
                $model, // model
                implode(', ', $groups), // serializer groups
                $paginationClassName,
            ),
        );
    }
}
