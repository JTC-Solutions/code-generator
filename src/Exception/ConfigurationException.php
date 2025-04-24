<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Exception;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\IConfiguration;

class ConfigurationException extends Exception
{
    /**
     * @param array<int,string>|array<int,IConfiguration> $existingItem
     */
    public static function itemAlreadySet(string $type, string|IConfiguration $item, array $existingItem): self
    {
        $itemIdentifier = is_string($item) ? $item : $item->getIdentifier();


        return new self(
            sprintf(
                'Attempted to add %s which already is set. Item %s is already set in existing in %s',
                $type,
                $itemIdentifier,
                json_encode($existingItem),
            ),
        );
    }

    /**
     * @param array<int,string>|array<int,IConfiguration> $existingItems
     */
    public static function orderAlreadySet(string $type, int $order, string|IConfiguration $item, array $existingItems): self
    {
        $itemIdentifier = is_string($item) ? $item : $item->getIdentifier();

        return new self(
            sprintf(
                'Attempted to add %s with order %d. Item attempted to add %s into existing items: %s',
                $type,
                $order,
                $itemIdentifier,
                json_encode($existingItems),
            ),
        );
    }

    /**
     * @param non-empty-string $message
     */
    public static function invalidConfigurationCombination(string $message): self
    {
        return new self($message);
    }
}
