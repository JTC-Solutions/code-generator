<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\CodeGenerator\MoveToOtherPackage\BaseController;
use JtcSolutions\CodeGenerator\CodeGenerator\MoveToOtherPackage\ErrorRequestJsonResponse;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration\MethodConfigurationBuilder;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Factory\MethodAttributeConfigurationFactory;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Factory\OpenApiDocConfigurationFactory;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ListControllerConfigurator extends BaseControllerConfigurator implements IControllerConfigurator
{
    protected const string METHOD_NAME = 'list';

    protected const string CONTROLLER_NAME_TEMPLATE = 'List%sController';

    protected const string NAMESPACE_TEMPLATE = 'App\\%s\App\Api\\%s';

    /**
     * @throws ConfigurationException
     */
    public function configure(Context $context): ControllerConfiguration
    {
        $builder = $this->createBuilder($context);

        $builder->addExtendedClass(BaseController::class);

        $this->configureUseStatements($builder, $context->entity);
        $this->configureOpenApiDocs($context->entity, $builder);

        return $builder->build();
    }

    public function createMethodConfiguration(Context $context): MethodConfiguration
    {
        $methodBuilder = new MethodConfigurationBuilder(self::METHOD_NAME, 'JsonResponse', $this->configureMethodBody($context->entity));
        $methodBuilder
            ->addAttribute(MethodAttributeConfigurationFactory::createListRouteAttribute($context->entity));
        return $methodBuilder->build();
    }

    /**
     * @param class-string $entity
     * @throws ConfigurationException
     */
    protected function configureUseStatements(ControllerConfigurationBuilder $builder, string $entity): void
    {
        $builder->addUseStatement(JsonResponse::class);
        $builder->addUseStatement(Route::class);

        // TODO: Handle automatic adding of use statements
        $builder->addUseStatement(OA\Tag::class);
        $builder->addUseStatement(OA\Response::class);
        $builder->addUseStatement(ErrorRequestJsonResponse::class);
        $builder->addUseStatement($entity);
    }

    /**
     * @param class-string $entity
     * @throws ConfigurationException
     */
    protected function configureOpenApiDocs(string $entity, ControllerConfigurationBuilder $builder): void
    {
        $openApiDocFactory = new OpenApiDocConfigurationFactory();

        $className = FQCNHelper::transformFQCNToEntityName($entity, false);

        $builder->addOpenApiDoc($openApiDocFactory->createTag($entity));
        $builder->addOpenApiDoc($openApiDocFactory->createJsonContentResponse(
            responseCode: 'Response::HTTP_OK',
            description: "List of {$className}, paginated by offset and limit.",
            type: $entity,
            groups: [
                StringUtils::firstToLowercase($className) . ':detail',
                'reference',
            ],
        ));
        $builder->addOpenApiDoc($openApiDocFactory->createModelResponse(
            responseCode: 'Response::HTTP_BAD_REQUEST',
            description: 'Request is invalid',
            type: ErrorRequestJsonResponse::class,
            groups: ['error'],
        ));
    }

    protected function configureMethodBody(string $entity): string
    {
        $className = FQCNHelper::transformFQCNToEntityName($entity, false);
        $lowercase = StringUtils::firstToLowercase($className);

        return <<<PHP
            \$this->checkPermissions({$className}::class, RequestAction::LIST);
            
            return \$this->json(\$entity, Response::HTTP_OK, [], ['groups' => ['{$lowercase}:list', 'reference']]);
        PHP;
    }
}
