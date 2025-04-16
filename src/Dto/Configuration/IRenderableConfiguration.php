<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\Configuration;

interface IRenderableConfiguration
{
    public function getNamespace(): string;

    public function getClassName(): string;

    /**
     * @return array<int, UseStatementConfiguration>
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
