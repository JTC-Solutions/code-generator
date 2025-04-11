<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration;

/**
 * @property string $namespace
 * @property string $className
 * @property array<int, string> $useStatements
 * @property array<int, string> $extends
 * @property array<int, string> $interfaces
 */
interface IRenderableConfiguration
{
    public function getNamespace(): string;

    public function getClassName(): string;

    /**
     * @return array<int, string>
     */
    public function getUseStatements(): array;

    /**
     * @return array<int, string>
     */
    public function getExtends(): array;

    /**
     * @return array<int, string>
     */
    public function getInterfaces(): array;
}
