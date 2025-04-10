<?php
declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Factory;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\Attribute\RouteAttributeConfiguration;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;

class MethodAttributeConfigurationFactory
{
    protected const string API_PREFIX = '/api/v1/';

    public static function createDetailRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToEntityName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: self::API_PREFIX . $kebabCase . '/{entity}',
            name: "{$snakeCase}_detail",
            methods: ['GET'],
        );
    }
}
