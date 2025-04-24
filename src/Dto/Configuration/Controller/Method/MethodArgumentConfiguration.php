<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\IConfiguration;

/**
 * Data Transfer Object representing the configuration for a single argument
 * within a method (e.g., a controller action or service method).
 *
 * This class holds information about the argument's name, type,
 * and potentially how it maps to request data or constructor property promotion.
 */
readonly class MethodArgumentConfiguration implements IConfiguration
{
    /**
     * Constructs a new MethodArgumentConfiguration instance.
     *
     * @param string $argumentName The name of the argument (e.g., 'userId', 'requestBody').
     * @param string $argumentType The PHP type hint for the argument (e.g., 'int', 'string', 'App\Dto\UserDto', 'UuidInterface|IEntity'). Can include union types.
     * @param bool $mapRequestPayloadAttribute Whether this argument should be annotated with #[MapRequestPayload] (relevant for controller actions). Defaults to false.
     * @param ?string $propertyType Defines the visibility ('public', 'private', 'protected') if the argument should be promoted to a constructor property. Set to null (default) to not promote the property.
     * @param ?bool $readonly If promoted to a property (when $propertyType is not null), determines if the property should be `readonly`. Set to null (default) to follow class readonly status or language defaults if applicable.
     */
    public function __construct(
        public string $argumentName,
        public string $argumentType,
        public bool $mapRequestPayloadAttribute = false,
        public ?string $propertyType = null, // public, private, protected, null to not promote
        public ?bool $readonly = null, // if the property should be readonly or not
    ) {
    }

    /**
     * Provides a unique identifier for this configuration instance,
     * potentially used for tracking or referencing during the code generation process.
     * The uniqueness is within the context where these configurations are managed.
     *
     * @return string A unique identifier combining the class name and the argument name.
     */
    public function getIdentifier(): string
    {
        return self::class . '_' . $this->argumentName;
    }
}
