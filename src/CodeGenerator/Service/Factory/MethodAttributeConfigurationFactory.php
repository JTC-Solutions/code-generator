<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Factory;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\Attribute\RouteAttributeConfiguration;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;

class MethodAttributeConfigurationFactory
{
    protected const string API_PREFIX = '/api/v1/';

    protected const string DETAIL_ROUTE_PATH = self::API_PREFIX . '%s/{entity}';

    protected const string LIST_ROUTE_PATH = self::API_PREFIX . '%s';

    protected const string DELETE_ROUTE_PATH = self::API_PREFIX . '%s/{id}';

    protected const string CREATE_ROUTE_PATH = self::API_PREFIX . '%s';

    protected const string UPDATE_ROUTE_PATH = self::API_PREFIX . '%s/{id}';

    public static function createDetailRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToEntityName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: sprintf(static::DETAIL_ROUTE_PATH, $kebabCase),
            name: "{$snakeCase}_detail",
            methods: ['GET'],
        );
    }

    public static function createListRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToEntityName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: sprintf(static::LIST_ROUTE_PATH, $kebabCase),
            name: "{$snakeCase}_list",
            methods: ['GET'],
        );
    }

    public static function createDeleteRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToEntityName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: sprintf(static::DELETE_ROUTE_PATH, $kebabCase),
            name: "{$snakeCase}_delete",
            methods: ['DELETE'],
        );
    }

    public static function createCreateRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToEntityName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: sprintf(self::CREATE_ROUTE_PATH, $kebabCase),
            name: "{$snakeCase}_create",
            methods: ['POST'],
        );
    }

    public static function createUpdateRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToEntityName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: sprintf(static::UPDATE_ROUTE_PATH, $kebabCase),
            name: "{$snakeCase}_update",
            methods: ['PUT'],
        );
    }
}
