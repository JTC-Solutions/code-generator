<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer;

interface ICodeRenderer
{
    public function renderCode(): string;
}
