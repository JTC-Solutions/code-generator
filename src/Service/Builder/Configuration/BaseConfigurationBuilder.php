<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\IConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;

/**
 * Abstract base class for configuration builders.
 * Provides common functionality for adding items to configuration arrays,
 * handling potential duplicates and ordering.
 */
abstract class BaseConfigurationBuilder
{
    /**
     * Adds an item (string or IConfiguration object) to an array, handling duplicates and optional ordering.
     *
     * @template T of string|IConfiguration
     * @param string $type A string identifying the type of item being added (e.g., 'useStatement', 'property') for error messages.
     * @param T $item The item to add.
     * @param array<int, T> $existingItems The array to add the item to.
     * @param int|null $order Optional specific index/order for the item. If null, item is appended.
     * @return array<int, T> The updated array with the item added.
     * @throws ConfigurationException If the item already exists or the specified order index is already taken.
     */
    protected function addItem(
        string $type,
        string|IConfiguration $item,
        array $existingItems,
        ?int $order = null,
    ): array {
        if ($this->itemExists($item, $existingItems) === true) {
            throw ConfigurationException::itemAlreadySet($type, $item, $existingItems);
        }

        if ($order === null) {
            $existingItems[] = $item;
            return $existingItems;
        }

        if (isset($existingItems[$order]) === true) {
            throw ConfigurationException::orderAlreadySet($type, $order, $item, $existingItems);
        }

        $existingItems[$order] = $item;

        return $existingItems;
    }

    /**
     * Checks if an item (string or IConfiguration object) already exists in an array.
     * For strings, it checks using in_array.
     * For IConfiguration objects, it checks based on their getIdentifier() method.
     *
     * @template T of string|IConfiguration
     * @param T $item The item to check for.
     * @param array<int, T> $existingItems The array to search within.
     * @return bool True if the item exists, false otherwise.
     */
    protected function itemExists(
        string|IConfiguration $item,
        array $existingItems,
    ): bool {
        if (is_string($item) === true) {
            return in_array($item, $existingItems, true);
        }

        /** @var IConfiguration $existingItem */
        foreach ($existingItems as $existingItem) {
            if ($existingItem->getIdentifier() === $item->getIdentifier()) {
                return true;
            }
        }

        return false;
    }
}
