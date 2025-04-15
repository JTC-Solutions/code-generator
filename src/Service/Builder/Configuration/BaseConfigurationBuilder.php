<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\IConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;

abstract class BaseConfigurationBuilder
{
    /**
     * @param array<int,string>|array<int,IConfiguration> $existingItems
     * @return array<int,string>|array<int,IConfiguration>
     * @throws ConfigurationException
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
     * @param array<int,string>|array<int,IConfiguration> $existingItems
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
