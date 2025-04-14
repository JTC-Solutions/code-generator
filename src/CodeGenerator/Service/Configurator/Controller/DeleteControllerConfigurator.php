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
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DeleteControllerConfigurator extends BaseControllerConfigurator implements IControllerConfigurator
{
    protected const string METHOD_NAME = 'delete';

    protected const string CONTROLLER_NAME_TEMPLATE = 'Delete%sController';

    public function configure(Context $context): ControllerConfiguration
    {
        $builder = $this->createBuilder($context);

        $this->configureOpenApiDocs($builder, $context);

        return $builder->build();
    }

    protected function createMethodConfiguration(Context $context): MethodConfiguration|null
    {
        $methodBuilder = new MethodConfigurationBuilder(static::METHOD_NAME, 'JsonResponse', $this->configureMethodBody($context->entityFQCN));
        $methodBuilder
            ->addArgument(new MethodArgumentConfiguration('id', 'UuidInterface'))
            ->addAttribute(MethodAttributeConfigurationFactory::createDeleteRouteAttribute($context->entityFQCN));

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

        // TODO: Handle automatic adding of use statements
        $builder->addUseStatement(ErrorRequestJsonResponse::class);
        $builder->addUseStatement(Model::class);
        $builder->addUseStatement(UuidInterface::class);
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
        $builder->addOpenApiDoc($openApiDocFactory->createEmptyResponse(
            responseCode: 'Response::HTTP_NO_CONTENT',
            description: "Delete of {$className}",
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
            \$this->checkPermissions({$className}::class, RequestAction::DELETE, ['id' => \$id]);
            
            // TODO: Implement
            
            return \$this->json(\$entity, Response::HTTP_CREATED, [], ['groups' => ['{$lowercase}:detail', 'reference']]);
        PHP;
    }
}
