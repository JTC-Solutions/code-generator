<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Controller;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use ReflectionException;

/**
 * Interface for controller configuration services.
 * Defines the contract for configuring the structure of a controller class.
 */
interface IControllerConfigurator
{
    /**
     * Configures the controller structure based on a target entity class.
     *
     * @param class-string $classFullyQualifiedClassName The fully qualified class name of the target entity.
     * @return ControllerConfiguration The configured controller structure DTO.
     * @throws ConfigurationException If configuration building fails (e.g., adding duplicate items).
     * @throws ReflectionException If reflection on the target class or related classes fails.
     * @throws Exception For other general errors during configuration.
     */
    public function configure(string $classFullyQualifiedClassName): ControllerConfiguration;
}
