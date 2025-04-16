# JTC Solutions Code Generator Bundle
Symfony bundle that helps generate boilerplate code for CRUD Controllers (List, Detail, Create, Update, Delete) and associated Request DTOs based on your Entities.

Designed primarily for Domain-Driven Design structured applications.

## Installation
1. **Require the bundle using Composer**:
```bash
composer require jtc-solutions/code-generator --dev
```
*(Note: Typically used as a dev dependency)*

2. **Enable the bundle**: \
If your application doesn't use Symfony Flex, you'll need to manually enable the bundle by adding it to your config/bundles.php file:
```php
// config/bundles.php
return [
    // ... other bundles
    JtcSolutions\CodeGenerator\JtcSolutionsCodeGeneratorBundle::class => ['dev' => true],
];

```
## Configuration
Create a configuration file (e.g., `config/packages/dev/jtc_solutions_code_generator.yaml`) to define how the generator should behave.
```yaml
# config/packages/dev/jtc_solutions_code_generator.yaml
jtc_solutions_code_generator:
    global:
        namespace:
            # Template for generated controller namespaces.
            # Placeholders: {domain}, {entity} (extracted from target entity FQCN)
            # Example: App\Domain\Catalog\Entity\Product -> domain=Catalog, entity=Product
            controllerNamespaceTemplate: 'App\UI\Controller\{domain}\{entity}' # Required

            # Template for generated DTO namespaces.
            # Placeholders: {domain}, {entity}
            dtoNamespaceTemplate: 'App\Application\Dto\{domain}\{entity}' # Required

        project:
            # Project source directory relative to kernel.project_dir
            # Example: 'src' or '.' if source is in the root
            projectDir: '%kernel.project_dir%/src' # Required

            # Base namespace corresponding to the projectDir
            # Example: 'App' for 'src/...'
            projectBaseNamespace: 'App' # Required

            # Fully qualified class name (FQCN) of the interface that all your entities implement.
            # Used by the DTO generator to identify entity properties.
            entityInterface: 'App\Domain\Shared\Entity\IEntity' # Required

            # Fully qualified class name (FQCN) of the class to use as a type hint
            # in DTOs when an entity property is detected.
            # Example: A property 'product' of type Product (implements IEntity)
            # will become 'product' of type EntityId in the DTO.
            dtoEntityReplacement: 'App\Domain\Shared\Dto\EntityId' # Required

            # Fully qualified class name (FQCN) of an interface that generated
            # Request DTOs (for Create/Update) should implement.
            requestDtoInterface: 'App\Application\Dto\IRequestDto' # Required

        openApi:
            # Fully qualified class name (FQCN) of the DTO used for error responses
            # in generated OpenAPI documentation.
            errorResponseClass: 'App\UI\Dto\ErrorResponse' # Required

            # Fully qualified class name (FQCN) of the DTO used for pagination metadata
            # in generated OpenAPI documentation for list endpoints.
            paginationClass: 'App\UI\Dto\PaginationMetadata' # Required
```
#### Placeholders
- `{domain}`: Extracted from the entity's namespace (e.g., `Catalog` from `App\Catalog\Domain\Entity\Product`).
- `{entity}`: The short class name of the entity (e.g., `Product` from `App\Domain\Catalog\Entity\Product`).

## Usage
The primary way to use the bundle is via the provided Symfony console command.
```bash
php bin/console jtc-solutions:generate-crud <TargetEntityFQCN>
```
*Note: The TargetEntityFQCN must be in quotes. Otherwise it is escaped.*

**Example**:
```bash
php bin/console jtc-solutions:generate-crud 'App\Domain\Catalog\Entity\Product'
```