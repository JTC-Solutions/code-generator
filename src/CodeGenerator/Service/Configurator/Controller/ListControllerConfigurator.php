<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\CodeGenerator\MoveToOtherPackage\BaseController;
use JtcSolutions\CodeGenerator\CodeGenerator\MoveToOtherPackage\ErrorRequestJsonResponse;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration\MethodConfigurationBuilder;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Factory\MethodAttributeConfigurationFactory;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Factory\OpenApiDocConfigurationFactory;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ListControllerConfigurator extends BaseControllerConfigurator implements IControllerConfigurator
{
    protected const string METHOD_NAME = 'list';

    protected const string CONTROLLER_NAME_TEMPLATE = 'List%sController';

    /**
     * @throws ConfigurationException
     */
    public function configure(Context $context): ControllerConfiguration
    {
        $builder = $this->createBuilder($context);

        $this->configureOpenApiDocs($builder, $context);

        return $builder->build();
    }

    public function createMethodConfiguration(Context $context): MethodConfiguration
    {
        $methodBuilder = new MethodConfigurationBuilder(self::METHOD_NAME, 'JsonResponse', $this->configureMethodBody($context->entityFQCN));
        $methodBuilder
            ->addAttribute(MethodAttributeConfigurationFactory::createListRouteAttribute($context->entityFQCN));
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
        $builder->addOpenApiDoc($openApiDocFactory->createJsonContentResponse(
            responseCode: 'Response::HTTP_OK',
            description: "List of {$className}, paginated by offset and limit.",
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
