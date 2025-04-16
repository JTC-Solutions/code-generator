<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\ClassPropertyMapper\TestClasses;

class ParentClass
{
    public string $inheritedProperty;

    protected string $protectedInherited = 'protected';

    private string $privateInherited = 'private';
}
