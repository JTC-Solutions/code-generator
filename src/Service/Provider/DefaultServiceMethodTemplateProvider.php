<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Provider;

use JtcSolutions\CodeGenerator\Dto\MappedProperty\MappedProperty;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;
use RuntimeException;

class DefaultServiceMethodTemplateProvider
{
    /**
     * @param class-string $classFullyQualifiedClassName
     * @param MappedProperty[] $properties
     * @return string php code of method
     */
    public static function provideCreateMethodTemplate(
        string $classFullyQualifiedClassName,
        array $properties,
    ): string {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $classVariableName = StringUtils::firstToLowercase($className);

        $assignments = [];

        // 1. Add ID generation
        // The calling configurator MUST ensure `use Ramsey\Uuid\Uuid;` is added.
        $assignments[] = sprintf('            %s: Uuid::uuid4(),', 'id');

        // 2. Add assignments for each property passed in
        foreach ($properties as $property) {
            $assignments[] = sprintf('            %s: $%s,', $property->name, $property->name);
        }

        $lastIndex = count($assignments) - 1;
        $assignments[$lastIndex] = rtrim($assignments[$lastIndex], ',');

        $assignmentString = implode("\n", $assignments);

        // 4. Construct the full method body including variable assignment, persistence, and return
        // NOTE: This hardcodes the use of $this->entityManager.
        $code = <<<PHP
        \${$classVariableName} = new {$className}(
{$assignmentString}
        );

        \$this->entityManager->persist(\${$classVariableName});
        \$this->entityManager->flush();

        return \${$classVariableName};
PHP;
        $indentedCode = preg_replace('/^/m', '    ', $code);
        if ($indentedCode === null) {
            throw new RuntimeException('Failed to indent code');
        }
        $indentedCode = ltrim($indentedCode);


        return $indentedCode;
    }

    /**
     * @param class-string $classFullyQualifiedClassName
     * @param MappedProperty[] $properties
     * @return string php code of method
     */
    public static function provideUpdateMethodTemplate(
        string $classFullyQualifiedClassName,
        array $properties,
    ): string {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $classVariableName = StringUtils::firstToLowercase($className);

        $setterCalls = [];

        // 1. Generate setter calls for each property
        foreach ($properties as $property) {
            // Skip 'id' property as it shouldn't be updated via setter usually
            if (strtolower($property->name) === 'id') {
                continue;
            }

            $setterMethodName = 'set' . ucfirst($property->name);


            // Assumes the new value is in a variable with the same name as the property
            $variableName = $property->name;

            // Add the setter call line with indentation
            $setterCalls[] = sprintf('        $%s->%s($%s);', $classVariableName, $setterMethodName, $variableName);
        }

        $setterBlock = implode("\n", $setterCalls);

        // 2. Construct the final code block including the return statement
        // NOTE: Persisting ($this->entityManager->flush() or $this->repository->save())
        // should be added by the caller (ServiceConfigurator) *after* this block.
        $code = <<<PHP
{$setterBlock}

        \$this->entityManager->flush();

        return \${$classVariableName};
PHP;
        // Adjust indentation of the final return statement if the setter block was empty
        if (empty($setterCalls)) {
            $code = "\n        // No properties to set.\n        // TODO: Add persistence logic here if needed.\n\n" . ltrim($code);
        }


        return $code;
    }

    /**
     * @param class-string $classFullyQualifiedClassName
     * @param MappedProperty[] $properties
     * @return string php code of method
     */
    public static function provideMapDataAndCallCreateMethodTemplate(
        string $classFullyQualifiedClassName,
        array $properties,
    ): string {
        $arguments = [];

        foreach ($properties as $property) {
            $arguments[] = sprintf('            %s: $%s->%s,', $property->name, 'requestBody', $property->name);
        }

        if (! empty($arguments)) {
            $lastIndex = count($arguments) - 1;
            $arguments[$lastIndex] = rtrim($arguments[$lastIndex], ',');
        }

        $argumentString = implode("\n", $arguments);

        // 3. Construct the final return statement calling the internal method
        $code = <<<PHP
        return \$this->create(
{$argumentString}
        );
PHP;

        // 4. Add indentation (assuming body starts at level 2)
        $indentedCode = preg_replace('/^/m', '    ', $code); // Add 4 spaces to start of each line
        if ($indentedCode === null) {
            throw new RuntimeException('Failed to indent code');
        }
        $indentedCode = ltrim($indentedCode); // Remove extra indent from first line


        return $indentedCode;
    }

    /**
     * @param class-string $classFullyQualifiedClassName
     * @param MappedProperty[] $properties
     * @return string php code of method
     */
    public static function provideMapDataAndCallUpdateMethodTemplate(
        string $classFullyQualifiedClassName,
        array $properties,
    ): string {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $classVariableName = StringUtils::firstToLowercase($className);
        $classIdVariableName = $classVariableName . 'Id';

        $arguments = [];

        $arguments[] = sprintf('            %s: $%s,', $classVariableName, $classVariableName);

        // 2. Create named arguments for the internal update call from DTO properties
        foreach ($properties as $property) {
            // Skip 'id' as it's usually not passed for updates this way
            if (strtolower($property->name) === 'id') {
                continue;
            }
            // Format: propertyName: $requestBodyVarName->propertyName,
            $arguments[] = sprintf('            %s: $%s->%s,', $property->name, 'requestBody', $property->name);
        }

        $lastIndex = count($arguments) - 1;
        $arguments[$lastIndex] = rtrim($arguments[$lastIndex], ',');

        $argumentString = implode("\n", $arguments);

        // 4. Construct the full method body including the if/else block and the call
        // Note the use of \$ for escaping variable names within the HEREDOC
        $code = <<<PHP
        if (\${$classIdVariableName} instanceof UuidInterface) {
            \${$classVariableName} = \$this->ensureEntityExists(['id' => \${$classIdVariableName}]);
        } else {
            \${$classVariableName} = \${$classIdVariableName};
        }

        return \$this->update(
{$argumentString}
        );
PHP;

        // 5. Add indentation (assuming body starts at level 2)
        $indentedCode = preg_replace('/^/m', '    ', $code); // Add 4 spaces to start of each line
        if ($indentedCode === null) {
            throw new RuntimeException('Failed to indent code');
        }
        $indentedCode = ltrim($indentedCode); // Remove extra indent from first line

        return $indentedCode;
    }

    /**
     * @param class-string $classFullyQualifiedClassName
     * @param MappedProperty[] $properties
     * @return string php code of method
     */
    public static function provideDeleteMethodTemplate(
        string $classFullyQualifiedClassName,
        array $properties,
    ): string {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $classVariableName = StringUtils::firstToLowercase($className);

        $code = <<<PHP
        if (\$id instanceof {$className}) {
            \$this->entityManager->remove(\${$classVariableName});
            return;
        }

        \${$classVariableName} = \$this->ensureEntityExists(['id' => \$id]);
        \$this->entityManager->remove(\${$classVariableName});
PHP;

        // 3. Add indentation (assuming body starts at level 2)
        $indentedCode = preg_replace('/^/m', '    ', $code); // Add 4 spaces to start of each line
        if ($indentedCode === null) {
            throw new RuntimeException('Failed to indent code');
        }
        $indentedCode = ltrim($indentedCode); // Remove extra indent from first line

        return $indentedCode;
    }
}
