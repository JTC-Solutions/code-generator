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
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Configures a controller for deleting entities by ID.
 * Sets up the 'delete' method, route, OpenAPI documentation, and necessary use statements.
 * Requires an ID (UuidInterface) as an argument.
 */
class DeleteControllerConfigurator extends BaseControllerConfigurator implements IControllerConfigurator
{
    /**
     * @const string Default name for the controller method.
     */
    protected const string DEFAULT_METHOD_NAME = 'delete';

    /**
     * @const string Default template for the controller class name.
     */
    protected const string DEFAULT_CONTROLLER_NAME_TEMPLATE = 'Delete%sController';

    /**
     * @param ContextProvider $contextProvider Provides context like namespaces, paths, and shared configuration.
     * @param string $methodName The name for the 'delete' method.
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
     * Configures the 'delete' controller structure.
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
     * Creates the method configuration for the 'delete' action.
     * Includes the ID (UuidInterface) argument and the Route attribute.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @return MethodConfiguration The configuration for the 'delete' method.
     * @throws ConfigurationException If building the method configuration fails.
     */
    protected function createMethodConfiguration(string $classFullyQualifiedClassName): MethodConfiguration|null
    {
        $methodBuilder = new MethodConfigurationBuilder($this->methodName, 'JsonResponse', $this->configureMethodBody($classFullyQualifiedClassName));
        $methodBuilder
            ->addArgument(new MethodArgumentConfiguration('id', 'UuidInterface'))
            ->addAttribute(MethodAttributeConfigurationFactory::createDeleteRouteAttribute($classFullyQualifiedClassName));

        return $methodBuilder->build();
    }

    /**
     * Configures use statements specific to the 'delete' controller.
     * Adds JsonResponse, Route, UuidInterface, Model, and the target entity class.
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
        $builder->addUseStatement(UuidInterface::class);
        $builder->addUseStatement($classFullyQualifiedClassName);
    }

    /**
     * Configures OpenAPI documentation attributes for the 'delete' action.
     * Includes tags, success (204), bad request (400), and not found (404) responses.
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
        $builder->addOpenApiDoc($openApiDocFactory->createEmptyResponse(
            responseCode: 'Response::HTTP_NO_CONTENT',
            description: "Delete of {$className}",
        ));
        $builder->addOpenApiDoc($openApiDocFactory->createModelResponse(
            responseCode: 'Response::HTTP_BAD_REQUEST',
            description: 'Request is invalid',
            type: $this->contextProvider->getErrorResponseClass(),
            groups: ['error'],
        ));
    }

    /**
     * Configures the method body for the 'delete' action.
     * Provides a placeholder implementation returning HTTP_NO_CONTENT.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @return string The code snippet for the method body.
     */
    protected function configureMethodBody(string $classFullyQualifiedClassName): string
    {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $lowercase = StringUtils::firstToLowercase($className);

        return <<<PHP
            // TODO: Implement
            
            return \$this->json(\$entity, Response::HTTP_CREATED, [], ['groups' => ['{$lowercase}:detail', 'reference']]);
        PHP;
    }
}
