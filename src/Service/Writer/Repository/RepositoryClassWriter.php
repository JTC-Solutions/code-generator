<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer\Repository;

use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\CodeGenerator\Service\Writer\BaseClassWriter;
use JtcSolutions\CodeGenerator\Service\Writer\IClassWriter;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use PhpParser\Error;

class RepositoryClassWriter extends BaseClassWriter implements IClassWriter
{
    public function write(
        string $classFullyQualifiedClassName,
        string $generatedFullyQualifiedClassName,
        string $code,
    ): string {
        $repositoryClassName = FQCNHelper::transformFQCNToShortClassName($generatedFullyQualifiedClassName);

        $filepath = sprintf('%s/%s.php', $this->contextProvider->getRepositoryPath($classFullyQualifiedClassName), $repositoryClassName);

        try {
            $this->parser->parse($code);
        } catch (Error $e) {
            throw TemplateNotValidPhpCodeException::create($repositoryClassName, $classFullyQualifiedClassName, $e);
        }

        $this->dumpFile($filepath, $code);

        return $filepath;
    }
}
