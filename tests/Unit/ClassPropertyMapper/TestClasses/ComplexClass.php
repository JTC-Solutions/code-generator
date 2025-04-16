<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\ClassPropertyMapper\TestClasses;

use DateTimeImmutable;
use DateTimeInterface;
use JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\EntityInterface;
use Ramsey\Uuid\UuidInterface;
use stdClass;

class ComplexClass implements EntityInterface
{
    public UuidInterface $id;

    public ?string $description;

    public DateTimeImmutable $createdAt;

    public ?DateTimeInterface $updatedAt;

    public ComplexClassAnotherClass $relatedObject;

    public ComplexClassAnotherClassInterface $optionalRelation;

    public $untypedVar;

    public mixed $mixedVar;

    public stdClass $standardObject;

    /**
     * @var string[]
     */
    public array $strings;
}
