<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Factory;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\Attribute\RouteAttributeConfiguration;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;

/**
 * Factory for creating RouteAttributeConfiguration objects for common CRUD actions.
 * Generates standard route paths and names based on the entity class name.
 */
class MethodAttributeConfigurationFactory
{
    /**
     * @const string Base prefix for all generated API routes.
     */
    protected const string API_PREFIX = '/api/v1/';

    /**
     * @const string Template for the detail route path (GET by ID/slug).
     */
    protected const string DETAIL_ROUTE_PATH = self::API_PREFIX . '%s/{entity}';

    /**
     * @const string Template for the list route path (GET collection).
     */
    protected const string LIST_ROUTE_PATH = self::API_PREFIX . '%s';

    /**
     * @const string Template for the delete route path (DELETE by ID).
     */
    protected const string DELETE_ROUTE_PATH = self::API_PREFIX . '%s/{id}';

    /**
     * @const string Template for the create route path (POST).
     */
    protected const string CREATE_ROUTE_PATH = self::API_PREFIX . '%s';

    /**
     * @const string Template for the update route path (PUT by ID).
     */
    protected const string UPDATE_ROUTE_PATH = self::API_PREFIX . '%s/{id}';

    /**
     * Creates a RouteAttributeConfiguration for a 'detail' action (GET /entity/{entity}).
     *
     * @param class-string $entity The fully qualified class name of the entity.
     * @return RouteAttributeConfiguration The configured route attribute.
     * @throws Exception If FQCN parsing or string conversion fails.
     */
    public static function createDetailRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToShortClassName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: sprintf(static::DETAIL_ROUTE_PATH, $kebabCase),
            name: "{$snakeCase}_detail",
            methods: ['GET'],
        );
    }

    /**
     * Creates a RouteAttributeConfiguration for a 'list' action (GET /entity).
     *
     * @param class-string $entity The fully qualified class name of the entity.
     * @return RouteAttributeConfiguration The configured route attribute.
     * @throws Exception If FQCN parsing or string conversion fails.
     */
    public static function createListRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToShortClassName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: sprintf(static::LIST_ROUTE_PATH, $kebabCase),
            name: "{$snakeCase}_list",
            methods: ['GET'],
        );
    }

    /**
     * Creates a RouteAttributeConfiguration for a 'delete' action (DELETE /entity/{id}).
     *
     * @param class-string $entity The fully qualified class name of the entity.
     * @return RouteAttributeConfiguration The configured route attribute.
     * @throws Exception If FQCN parsing or string conversion fails.
     */
    public static function createDeleteRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToShortClassName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: sprintf(static::DELETE_ROUTE_PATH, $kebabCase),
            name: "{$snakeCase}_delete",
            methods: ['DELETE'],
        );
    }

    /**
     * Creates a RouteAttributeConfiguration for a 'create' action (POST /entity).
     *
     * @param class-string $entity The fully qualified class name of the entity.
     * @return RouteAttributeConfiguration The configured route attribute.
     * @throws Exception If FQCN parsing or string conversion fails.
     */
    public static function createCreateRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToShortClassName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: sprintf(self::CREATE_ROUTE_PATH, $kebabCase),
            name: "{$snakeCase}_create",
            methods: ['POST'],
        );
    }

    /**
     * Creates a RouteAttributeConfiguration for an 'update' action (PUT /entity/{id}).
     *
     * @param class-string $entity The fully qualified class name of the entity.
     * @return RouteAttributeConfiguration The configured route attribute.
     * @throws Exception If FQCN parsing or string conversion fails.
     */
    public static function createUpdateRouteAttribute(
        string $entity,
    ): RouteAttributeConfiguration {
        $className = FQCNHelper::transformFQCNToShortClassName($entity, false);
        $kebabCase = StringUtils::toKebabCase($className);
        $snakeCase = StringUtils::toSnakeCase($className);

        return new RouteAttributeConfiguration(
            path: sprintf(static::UPDATE_ROUTE_PATH, $kebabCase),
            name: "{$snakeCase}_update",
            methods: ['PUT'],
        );
    }
}
