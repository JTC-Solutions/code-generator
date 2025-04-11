<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\CodeGenerator\MoveToOtherPackage\BaseEntityController;
use JtcSolutions\CodeGenerator\CodeGenerator\MoveToOtherPackage\ErrorRequestJsonResponse;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration\MethodConfigurationBuilder;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Factory\MethodAttributeConfigurationFactory;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Factory\OpenApiDocConfigurationFactory;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class UpdateControllerConfigurator extends BaseControllerConfigurator implements IControllerConfigurator
{
    protected const string METHOD_NAME = 'update';

    protected const string ARGUMENT_NAME = 'request';

    protected const string CONTROLLER_NAME_TEMPLATE = 'Update%sController';

    /**
     * @throws ConfigurationException
     */
    public function configure(Context $context): ControllerConfiguration
    {
        $builder = $this->createBuilder($context);

        $builder->addExtendedClass(BaseEntityController::class);

        $this->configureOpenApiDocs($builder, $context);

        return $builder->build();
    }

    public function createMethodConfiguration(Context $context): MethodConfiguration
    {
        $entityClassName = FQCNHelper::transformFQCNToEntityName($context->entityFQCN, false);

        $methodBuilder = new MethodConfigurationBuilder(self::METHOD_NAME, 'JsonResponse', $this->configureMethodBody($context->entityFQCN));
        $methodBuilder
            ->addArgument(new MethodArgumentConfiguration('id', 'UuidInterface'))
            ->addArgument(new MethodArgumentConfiguration(self::ARGUMENT_NAME, $entityClassName . 'UpdateRequest')) // TODO: here and on create is manually name
            ->addAttribute(MethodAttributeConfigurationFactory::createUpdateRouteAttribute($context->entityFQCN));

        return $methodBuilder->build();
    }

    /**
     * @throws ConfigurationException
     */
    protected function configureUseStatements(ControllerConfigurationBuilder $builder, Context $context): void
    {
        parent::configureUseStatements($builder, $context);

        $builder->addUseStatement(JsonResponse::class);
        $builder->addUseStatement(Route::class);
        $builder->addUseStatement(UuidInterface::class);

        // TODO: Handle automatic adding of use statements
        $builder->addUseStatement(OA\Tag::class);
        $builder->addUseStatement(OA\Response::class);
        $builder->addUseStatement(ErrorRequestJsonResponse::class);
        $builder->addUseStatement(Model::class);
        $builder->addUseStatement($context->entityFQCN);
    }

    /**
     * @throws ConfigurationException
     */
    protected function configureOpenApiDocs(ControllerConfigurationBuilder $builder, Context $context): void
    {
        $openApiDocFactory = new OpenApiDocConfigurationFactory();

        $className = FQCNHelper::transformFQCNToEntityName($context->entityFQCN, false);

        $builder->addOpenApiDoc($openApiDocFactory->createTag($context->entityFQCN));
        $builder->addOpenApiDoc($openApiDocFactory->createModelResponse(
            responseCode: 'Response::HTTP_OK',
            description: "Update of {$className}",
            type: $context->entityFQCN,
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
        $builder->addOpenApiDoc($openApiDocFactory->createModelResponse(
            responseCode: 'Response::HTTP_CONFLICT',
            description: 'Entity already exists.',
            type: ErrorRequestJsonResponse::class,
            groups: ['error'],
        ));
    }

    protected function configureMethodBody(string $entity): string
    {
        $className = FQCNHelper::transformFQCNToEntityName($entity, false);
        $lowercase = StringUtils::firstToLowercase($className);

        return <<<PHP
            \$this->checkPermissions({$className}::class, RequestAction::CREATE);
            
            // TODO: Implement
            
            return \$this->json(\$entity, Response::HTTP_OK, [], ['groups' => ['{$lowercase}:detail', 'reference']]);
        PHP;
    }
}
