<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass;

use Ramsey\Uuid\UuidInterface;

class NestedEntityClass implements EntityInterface
{
    public UuidInterface $id;
}
