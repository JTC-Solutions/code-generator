<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Factory;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\OpenApiDoc\OpenApiDocResponseConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\OpenApiDoc\OpenApiDocTagConfiguration;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;

class OpenApiDocConfigurationFactory
{
    protected const string RESPONSE_MODEL_TEMPLATE = 'new Model(type: %s::class, groups: [\'%s\'])';

    /**
     * @param class-string $name
     */
    public function createTag(string $name): OpenApiDocTagConfiguration
    {
        $className = FQCNHelper::transformFQCNToEntityName($name, false);
        $classNameSnakeCase = StringUtils::toSnakeCase($className);

        return new OpenApiDocTagConfiguration($classNameSnakeCase);
    }

    /**
     * @param string[] $groups
     * @param class-string|string $type
     */
    public function createResponse(
        string $responseCode,
        string $description,
        string $type,
        array $groups,
    ): OpenApiDocResponseConfiguration {
        $model = FQCNHelper::transformFQCNToEntityName($type, false);

        return new OpenApiDocResponseConfiguration(
            response: $responseCode,
            description: $description,
            content: sprintf(self::RESPONSE_MODEL_TEMPLATE, $model, implode(', ', $groups)),
        );
    }
}
