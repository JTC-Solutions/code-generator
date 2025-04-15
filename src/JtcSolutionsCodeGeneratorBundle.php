<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator;

use JtcSolutions\CodeGenerator\DependencyInjection\JtcSolutionsCodeGeneratorExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class JtcSolutionsCodeGeneratorBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new JtcSolutionsCodeGeneratorExtension();
        }

        return $this->extension;
    }
}
