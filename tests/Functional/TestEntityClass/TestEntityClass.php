<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class TestEntityClass implements EntityInterface
{
    public UuidInterface $id;

    public ?string $description;

    public DateTimeImmutable $createdAt;

    public NestedEntityClass $nested;

    public string $ignoredProperty;
}
