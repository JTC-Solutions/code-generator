<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Controller;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
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
 * Configures a controller for fetching the details of a single entity.
 * Sets up the 'detail' method, route (using parameter conversion for the entity),
 * OpenAPI documentation, and necessary use statements.
 */
class DetailControllerConfigurator extends BaseControllerConfigurator implements IControllerConfigurator
{
    /**
     * @const string Default name for the controller method.
     */
    protected const string DEFAULT_METHOD_NAME = 'detail';

    /**
     * @const string Default name for the entity argument (from param converter).
     */
    protected const string DEFAULT_ARGUMENT_NAME = 'entity';

    /**
     * @const string Default template for the controller class name.
     */
    protected const string DEFAULT_CONTROLLER_NAME_TEMPLATE = 'Detail%sController';

    /**
     * @param ContextProvider $contextProvider Provides context like namespaces, paths, and shared configuration.
     * @param string $methodName The name for the 'detail' method.
     * @param string $controllerNameTemplate Template for the controller class name.
     * @param bool $callParentConstructor Whether to call parent::__construct in the generated controller.
     * @param string $argumentName The name for the entity argument injected via parameter conversion.
     */
    public function __construct(
        ContextProvider $contextProvider,
        string $methodName = self::DEFAULT_METHOD_NAME,
        string $controllerNameTemplate = self::DEFAULT_CONTROLLER_NAME_TEMPLATE,
        bool $callParentConstructor = false,
        protected readonly string $argumentName = self::DEFAULT_ARGUMENT_NAME,
    ) {
        parent::__construct($contextProvider, $methodName, $controllerNameTemplate, $callParentConstructor);
    }

    /**
     * Configures the 'detail' controller structure.
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
     * Creates the method configuration for the 'detail' action.
     * Includes the entity argument (type-hinted to the target entity class) and the Route attribute.
     * Relies on Symfony's ParamConverter to fetch the entity based on the route parameter.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @return MethodConfiguration The configuration for the 'detail' method.
     * @throws ConfigurationException If building the method configuration fails.
     * @throws Exception If FQCN parsing fails.
     */
    public function createMethodConfiguration(string $classFullyQualifiedClassName): MethodConfiguration
    {
        $entityClassName = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);

        $methodBuilder = new MethodConfigurationBuilder($this->methodName, 'JsonResponse', $this->configureMethodBody($classFullyQualifiedClassName));
        $methodBuilder
            ->addArgument(new MethodArgumentConfiguration($this->argumentName, $entityClassName))
            ->addAttribute(MethodAttributeConfigurationFactory::createDetailRouteAttribute($classFullyQualifiedClassName));

        return $methodBuilder->build();
    }

    /**
     * Configures use statements specific to the 'detail' controller.
     * Adds JsonResponse, Route, Model, and the target entity class.
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
    }

    /**
     * Configures OpenAPI documentation attributes for the 'detail' action.
     * Includes tags, success (200), and not found (404) responses.
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
        $builder->addOpenApiDoc($openApiDocFactory->createModelResponse(
            responseCode: 'Response::HTTP_OK',
            description: "Detail of {$className}",
            type: $classFullyQualifiedClassName,
            groups: [
                StringUtils::firstToLowercase($className) . ':detail',
                'reference',
            ],
        ));
        $builder->addOpenApiDoc($openApiDocFactory->createModelResponse(
            responseCode: 'Response::HTTP_BAD_REQUEST',
            description: 'Request is invalid',
            type: $this->contextProvider->getErrorResponseClass(),
            groups: ['error'],
        ));
    }

    /**
     * Configures the method body for the 'detail' action.
     * Provides a simple implementation that returns the injected entity.
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
        return \$this->json(\$entity, Response::HTTP_OK, [], ['groups' => ['{$lowercase}:detail', 'reference']]);
        PHP;
    }
}
