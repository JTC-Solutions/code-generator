<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Controller;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\MethodConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\Factory\MethodAttributeConfigurationFactory;
use JtcSolutions\CodeGenerator\Service\Factory\OpenApiDocConfigurationFactory;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Configures a controller for listing entities, potentially with pagination.
 * Sets up the 'list' method, route, OpenAPI documentation (including pagination parameters),
 * and necessary use statements.
 */
class ListControllerConfigurator extends BaseControllerConfigurator implements IControllerConfigurator
{
    /**
     * @const string Default name for the controller method.
     */
    protected const string DEFAULT_METHOD_NAME = 'list';

    /**
     * @const string Default template for the controller class name.
     */
    protected const string DEFAULT_CONTROLLER_NAME_TEMPLATE = 'List%sController';

    /**
     * @param ContextProvider $contextProvider Provides context like namespaces, paths, and shared configuration (pagination class).
     * @param string $methodName The name for the 'list' method.
     * @param string $controllerNameTemplate Template for the controller class name.
     * @param bool $callParentConstructor Whether to call parent::__construct in the generated controller.
     */
    public function __construct(
        ContextProvider $contextProvider,
        string $methodName = self::DEFAULT_METHOD_NAME,
        string $controllerNameTemplate = self::DEFAULT_CONTROLLER_NAME_TEMPLATE,
        bool $callParentConstructor = false,
    ) {
        parent::__construct($contextProvider, $methodName, $controllerNameTemplate, $callParentConstructor);
    }

    /**
     * Configures the 'list' controller structure.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @return ControllerConfiguration The configured controller structure DTO.
     * @throws ConfigurationException If configuration building fails.
     * @throws Exception If FQCN parsing fails.
     */
    public function configure(string $classFullyQualifiedClassName): ControllerConfiguration
    {
        $builder = $this->createBuilder($classFullyQualifiedClassName);

        $this->configureOpenApiDocs($builder, $classFullyQualifiedClassName);

        return $builder->build();
    }

    /**
     * Creates the method configuration for the 'list' action.
     * Includes the Route attribute. No arguments by default, assumes query parameters for pagination/filtering.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @return MethodConfiguration The configuration for the 'list' method.
     * @throws ConfigurationException If building the method configuration fails.
     */
    public function createMethodConfiguration(string $classFullyQualifiedClassName): MethodConfiguration
    {
        $methodBuilder = new MethodConfigurationBuilder($this->methodName, 'JsonResponse', $this->configureMethodBody($classFullyQualifiedClassName));
        $methodBuilder
            ->addAttribute(MethodAttributeConfigurationFactory::createListRouteAttribute($classFullyQualifiedClassName));
        return $methodBuilder->build();
    }

    /**
     * Configures use statements specific to the 'list' controller.
     * Adds JsonResponse, Route, Model, the target entity class, and the pagination DTO class.
     *
     * @param ControllerConfigurationBuilder $builder The builder instance.
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @throws ConfigurationException If adding use statements fails.
     */
    protected function configureUseStatements(ControllerConfigurationBuilder $builder, string $classFullyQualifiedClassName): void
    {
        parent::configureUseStatements($builder, $classFullyQualifiedClassName);

        $builder->addUseStatement(JsonResponse::class);
        $builder->addUseStatement(Route::class);

        // TODO: Handle automatic adding of use statements
        $builder->addUseStatement(Model::class);
        $builder->addUseStatement($classFullyQualifiedClassName);
        $builder->addUseStatement($this->contextProvider->paginationClass);
    }

    /**
     * Configures OpenAPI documentation attributes for the 'list' action.
     * Includes tags, success (200 with pagination structure), bad request (400),
     * and query parameters for pagination (offset, limit).
     *
     * @param ControllerConfigurationBuilder $builder The builder instance.
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @throws ConfigurationException If adding OpenAPI docs fails.
     * @throws Exception If FQCN parsing fails.
     */
    protected function configureOpenApiDocs(ControllerConfigurationBuilder $builder, string $classFullyQualifiedClassName): void
    {
        $openApiDocFactory = new OpenApiDocConfigurationFactory();

        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);

        $builder->addOpenApiDoc($openApiDocFactory->createTag($classFullyQualifiedClassName));
        $builder->addOpenApiDoc($openApiDocFactory->createJsonContentResponse(
            responseCode: 'Response::HTTP_OK',
            description: "List of {$className}, paginated by offset and limit.",
            type: $className,
            groups: [
                StringUtils::firstToLowercase($className) . ':detail',
                'reference',
            ],
            paginationFullyQualifiedClassName: $this->contextProvider->getPaginationClass(),
        ));
        $builder->addOpenApiDoc($openApiDocFactory->createModelResponse(
            responseCode: 'Response::HTTP_BAD_REQUEST',
            description: 'Request is invalid',
            type: $this->contextProvider->getErrorResponseClass(),
            groups: ['error'],
        ));
    }

    /**
     * Configures the method body for the 'list' action.
     * Provides a placeholder implementation.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @return string The code snippet for the method body.
     * @throws Exception If FQCN parsing fails.
     */
    protected function configureMethodBody(string $classFullyQualifiedClassName): string
    {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $lowercase = StringUtils::firstToLowercase($className);

        return <<<PHP
            return \$this->json(\$entity, Response::HTTP_OK, [], ['groups' => ['{$lowercase}:list', 'reference']]);
        PHP;
    }
}
