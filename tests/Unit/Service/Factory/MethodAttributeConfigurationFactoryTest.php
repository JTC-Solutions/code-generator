<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\Service\Factory;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\Attribute\RouteAttributeConfiguration;
use JtcSolutions\CodeGenerator\Service\Factory\MethodAttributeConfigurationFactory;
use PHPUnit\Framework\TestCase;

class MethodAttributeConfigurationFactoryTest extends TestCase
{
    /**
     * @dataProvider routeDataProvider
     */
    public function testCreateRouteAttributes(
        string $entityFqcn,
        string $method,
        string $expectedPath,
        string $expectedName,
        array $expectedMethods,
    ): void {
        /** @var RouteAttributeConfiguration $attribute */
        $attribute = MethodAttributeConfigurationFactory::{$method}($entityFqcn);

        self::assertInstanceOf(RouteAttributeConfiguration::class, $attribute);
        self::assertSame($expectedPath, $attribute->path);
        self::assertSame($expectedName, $attribute->name);
        self::assertSame($expectedMethods, $attribute->methods);
        self::assertStringStartsWith(RouteAttributeConfiguration::class, $attribute->getIdentifier());
    }

    public static function routeDataProvider(): array
    {
        return [
            'Detail Route' => [
                'App\Entity\Product\ProductCategory', // entityFqcn
                'createDetailRouteAttribute', // method
                '/api/v1/product-category/{entity}', // expectedPath
                'product_category_detail', // expectedName
                ['GET'], // expectedMethods
            ],
            'List Route' => [
                'App\Entity\User', // entityFqcn
                'createListRouteAttribute', // method
                '/api/v1/user', // expectedPath
                'user_list', // expectedName
                ['GET'], // expectedMethods
            ],
            'Create Route' => [
                'App\Entity\Order\Order', // entityFqcn
                'createCreateRouteAttribute', // method
                '/api/v1/order', // expectedPath
                'order_create', // expectedName
                ['POST'], // expectedMethods
            ],
            'Update Route' => [
                'App\Entity\Invoice', // entityFqcn
                'createUpdateRouteAttribute', // method
                '/api/v1/invoice/{id}', // expectedPath
                'invoice_update', // expectedName
                ['PUT'], // expectedMethods
            ],
            'Delete Route' => [
                'Vendor\Package\Model\CustomerData', // entityFqcn
                'createDeleteRouteAttribute', // method
                '/api/v1/customer-data/{id}', // expectedPath
                'customer_data_delete', // expectedName
                ['DELETE'], // expectedMethods
            ],
        ];
    }
}
