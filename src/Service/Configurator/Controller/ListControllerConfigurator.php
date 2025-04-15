<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Controller;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\MethodConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\Factory\MethodAttributeConfigurationFactory;
use JtcSolutions\CodeGenerator\Service\Factory\OpenApiDocConfigurationFactory;
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
    public function configure(string $classFullyQualifiedClassName): ControllerConfiguration
    {
        $builder = $this->createBuilder($classFullyQualifiedClassName);

        $this->configureOpenApiDocs($builder, $classFullyQualifiedClassName);

        return $builder->build();
    }

    /**
     * @param class-string $classFullyQualifiedClassName
     * @throws ConfigurationException
     */
    public function createMethodConfiguration(string $classFullyQualifiedClassName): MethodConfiguration
    {
        $methodBuilder = new MethodConfigurationBuilder(self::METHOD_NAME, 'JsonResponse', $this->configureMethodBody($classFullyQualifiedClassName));
        $methodBuilder
            ->addAttribute(MethodAttributeConfigurationFactory::createListRouteAttribute($classFullyQualifiedClassName));
        return $methodBuilder->build();
    }

    /**
     * @param class-string $classFullyQualifiedClassName
     * @throws ConfigurationException
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
     * @param class-string $classFullyQualifiedClassName
     * @throws ConfigurationException
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
        ));
        $builder->addOpenApiDoc($openApiDocFactory->createModelResponse(
            responseCode: 'Response::HTTP_BAD_REQUEST',
            description: 'Request is invalid',
            type: $this->contextProvider->getErrorResponseClass(),
            groups: ['error'],
        ));
    }

    /**
     * @param class-string $classFullyQualifiedClassName
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
