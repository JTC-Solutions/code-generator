<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\Service\Factory;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\Attribute\RouteAttributeConfiguration;
use JtcSolutions\CodeGenerator\Service\Factory\MethodAttributeConfigurationFactory;
use PHPUnit\Framework\TestCase;

class MethodAttributeConfigurationFactoryTest extends TestCase
{
    private const FQCN = 'App\Domain\Product\Entity\ProductItem';

    public function testCreateDetailRouteAttribute(): void
    {
        $attribute = MethodAttributeConfigurationFactory::createDetailRouteAttribute(self::FQCN);

        self::assertInstanceOf(RouteAttributeConfiguration::class, $attribute);
        self::assertSame('/api/v1/product-item/{entity}', $attribute->path);
        self::assertSame('product_item_detail', $attribute->name);
        self::assertSame(['GET'], $attribute->methods);
        self::assertSame(
            '#[Route(\'/api/v1/product-item/{entity}\', name: \'product_item_detail\', methods: [\'GET\'])]',
            $attribute->render(),
        );
        self::assertSame(
            RouteAttributeConfiguration::class . '_/api/v1/product-item/{entity}',
            $attribute->getIdentifier(),
        );
    }

    public function testCreateListRouteAttribute(): void
    {
        $attribute = MethodAttributeConfigurationFactory::createListRouteAttribute(self::FQCN);

        self::assertInstanceOf(RouteAttributeConfiguration::class, $attribute);
        self::assertSame('/api/v1/product-item', $attribute->path);
        self::assertSame('product_item_list', $attribute->name);
        self::assertSame(['GET'], $attribute->methods);
        self::assertSame(
            '#[Route(\'/api/v1/product-item\', name: \'product_item_list\', methods: [\'GET\'])]',
            $attribute->render(),
        );
        self::assertSame(
            RouteAttributeConfiguration::class . '_/api/v1/product-item',
            $attribute->getIdentifier(),
        );
    }

    public function testCreateDeleteRouteAttribute(): void
    {
        $attribute = MethodAttributeConfigurationFactory::createDeleteRouteAttribute(self::FQCN);

        self::assertInstanceOf(RouteAttributeConfiguration::class, $attribute);
        self::assertSame('/api/v1/product-item/{id}', $attribute->path);
        self::assertSame('product_item_delete', $attribute->name);
        self::assertSame(['DELETE'], $attribute->methods);
        self::assertSame(
            '#[Route(\'/api/v1/product-item/{id}\', name: \'product_item_delete\', methods: [\'DELETE\'])]',
            $attribute->render(),
        );
        self::assertSame(
            RouteAttributeConfiguration::class . '_/api/v1/product-item/{id}',
            $attribute->getIdentifier(),
        );
    }

    public function testCreateCreateRouteAttribute(): void
    {
        $attribute = MethodAttributeConfigurationFactory::createCreateRouteAttribute(self::FQCN);

        self::assertInstanceOf(RouteAttributeConfiguration::class, $attribute);
        self::assertSame('/api/v1/product-item', $attribute->path);
        self::assertSame('product_item_create', $attribute->name);
        self::assertSame(['POST'], $attribute->methods);
        self::assertSame(
            '#[Route(\'/api/v1/product-item\', name: \'product_item_create\', methods: [\'POST\'])]',
            $attribute->render(),
        );
        self::assertSame(
            RouteAttributeConfiguration::class . '_/api/v1/product-item',
            $attribute->getIdentifier(),
        );
    }

    public function testCreateUpdateRouteAttribute(): void
    {
        $attribute = MethodAttributeConfigurationFactory::createUpdateRouteAttribute(self::FQCN);

        self::assertInstanceOf(RouteAttributeConfiguration::class, $attribute);
        self::assertSame('/api/v1/product-item/{id}', $attribute->path);
        self::assertSame('product_item_update', $attribute->name);
        self::assertSame(['PUT'], $attribute->methods);
        self::assertSame(
            '#[Route(\'/api/v1/product-item/{id}\', name: \'product_item_update\', methods: [\'PUT\'])]',
            $attribute->render(),
        );
        self::assertSame(
            RouteAttributeConfiguration::class . '_/api/v1/product-item/{id}',
            $attribute->getIdentifier(),
        );
    }
}
