<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodArgumentConfiguration;
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

class DetailControllerConfigurator
{
    protected const string METHOD_NAME = 'detail';

    protected const string ARGUMENT_NAME = 'entity';

    protected const string CONTROLLER_NAME_TEMPLATE = 'Detail%sController';

    protected const string NAMESPACE_TEMPLATE = 'App\\%s\App\Api\\%s';

    /**
     * @param class-string $entity
     * @throws ConfigurationException
     */
    public function configure(string $domain, string $entity): ControllerConfiguration
    {
        $entityClassName = FQCNHelper::transformFQCNToEntityName($entity, false);

        $builder = new ControllerConfigurationBuilder(
            className: sprintf(self::CONTROLLER_NAME_TEMPLATE, $entityClassName),
            namespace: sprintf(self::NAMESPACE_TEMPLATE, $domain, $entityClassName),
            method: $this->createMethodConfiguration($entity),
        );

        $builder->addExtendedClass(BaseController::class);

        $this->configureUseStatements($builder, $entity);
        $this->configureOpenApiDocs($entity, $builder);

        return $builder->build();
    }

    /**
     * @param class-string $entity
     */
    public function createMethodConfiguration(string $entity): MethodConfiguration
    {
        $entityClassName = FQCNHelper::transformFQCNToEntityName($entity, false);

        $methodBuilder = new MethodConfigurationBuilder(self::METHOD_NAME, 'JsonResponse', $this->configureMethodBody($entity));
        $methodBuilder
            ->addArgument(new MethodArgumentConfiguration(self::ARGUMENT_NAME, $entityClassName))
            ->addAttribute(MethodAttributeConfigurationFactory::createDetailRouteAttribute($entity));

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
        $builder->addOpenApiDoc($openApiDocFactory->createResponse(
            responseCode: 'Response::HTTP_OK',
            description: "Detail of {$className}",
            type: $entity,
            groups: [
                StringUtils::firstToLowercase($className) . ':detail',
                'reference',
            ],
        ));
        $builder->addOpenApiDoc($openApiDocFactory->createResponse(
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
            \$this->checkPermissions({$className}::class, RequestAction::DETAIL);
            
            return \$this->json(\$entity, Response::HTTP_OK, [], ['groups' => ['{$lowercase}:detail', 'reference']]);
        PHP;
    }
}
