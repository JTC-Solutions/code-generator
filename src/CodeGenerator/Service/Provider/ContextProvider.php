<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Provider;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;

final class ContextProvider
{
    private readonly Context $context;

    /**
     * @param class-string $entity
     * @param class-string[] $extendedClasses
     */
    public function __construct(
        string $controllerNamespace,
        string $dtoNamespace,
        string $entity,
        array $extendedClasses = []
    ) {
        $this->context = new Context(
            entityFQCN: $entity,
            entityNamespace: $this->getNamespaceFromFQCN($entity),
            entityPath: $this->namespaceToPath($this->getNamespaceFromFQCN($entity)),
            controllerPath: $this->namespaceToPath($controllerNamespace),
            controllerNamespace: $controllerNamespace,
            dtoPath: $this->namespaceToPath($dtoNamespace),
            dtoNamespace: $dtoNamespace,
            extendedClasses: $extendedClasses
        );
    }

    public static function getBaseNamespace(): string
    {
        return 'App';
    }

    public static function getProjectDir(): string
    {
        return 'project';
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public static function namespaceToPath(string $namespace): string
    {
        $baseNamespace = self::getBaseNamespace();
        $basePath = self::getProjectDir();

        if (strpos($namespace, $baseNamespace . '\\') === 0) {
            $relativeNamespace = substr($namespace, strlen($baseNamespace . '\\'));
        } else {
            $relativeNamespace = $namespace;
        }

        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeNamespace);

        // Join with base path
        return rtrim(rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $relativePath, DIRECTORY_SEPARATOR);
    }

    public static function pathToNamespace(string $path): string
    {
        $baseNamespace = self::getBaseNamespace();
        $basePath = self::getProjectDir();

        // Normalize slashes
        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $normalizedBasePath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $basePath), DIRECTORY_SEPARATOR);

        // Remove the base path prefix
        if (strpos($normalizedPath, $normalizedBasePath . DIRECTORY_SEPARATOR) === 0) {
            $relativePath = substr($normalizedPath, strlen($normalizedBasePath . DIRECTORY_SEPARATOR));
        } else {
            $relativePath = $normalizedPath;
        }

        // Convert slashes to namespace backslashes
        $relativeNamespace = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

        // Join with base namespace
        return rtrim($baseNamespace, '\\') . '\\' . $relativeNamespace;
    }

    public function getNamespaceFromFQCN(string $fqcn): string
    {
        $fqcn = ltrim($fqcn, '\\'); // <-- Add this line

        // Find the last backslash
        $lastBackslashPos = strrpos($fqcn, '\\');

        // If no backslash, it's a global class (no namespace)
        if ($lastBackslashPos === false) {
            return '';
        }

        // Return everything before the last backslash
        return substr($fqcn, 0, $lastBackslashPos);
    }
}
