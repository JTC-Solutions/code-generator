<?php

namespace JtcSolutions\CodeGenerator;

use JtcSolutions\CodeGenerator\DependencyInjection\JtcSolutionsCodeGeneratorExtension;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class JtcSolutionsCodeGeneratorBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new JtcSolutionsCodeGeneratorExtension();
        }
        return $this->extension;
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}