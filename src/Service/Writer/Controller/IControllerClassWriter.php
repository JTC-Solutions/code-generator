<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer\Controller;

use JtcSolutions\CodeGenerator\Service\Writer\IClassWriter;

/**
 * Marker interface specifically for Controller class writers.
 * Extends the base IClassWriter interface.
 * Allows for specific dependency injection targeting controller writers.
 */
interface IControllerClassWriter extends IClassWriter
{
}
