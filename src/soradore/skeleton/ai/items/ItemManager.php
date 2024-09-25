<?php

/*
 *
 *  _ __  _ __  ___  ___
 * | '_ \| '_ \/ __|/ _ \
 * | | | | | | \__ \  __/
 * |_| |_|_| |_|___/\___|
 *
 * This program is free software: you can redistribute it and/or modify
 * It under the terms of the MIT License.
 *
 * @author nnse (nonsense)
 * @link   https://github.com/nnse
 * @license https://opensource.org/licenses/MIT
 *
 *
 */

namespace soradore\skeleton\ai\items;

use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalItemDataHandlers;

final class ItemManager
{
    use SingletonTrait;

    public const TAG_WRAPPER_ITEM_CLASS = "wrapper_item_class"; // TAG_String

    protected $wrapperItems = [];

    /**
     * @param WrapperItem $wrapperItem
     * @param bool $addCreativeInventory
     * @return void
     */
    public function register(Item $item, string $itemTypeName, string $namespace, bool $addCreativeInventory = true) : void
    {
        /** @var Item */
        $runtimeId = $item->getTypeId();

        (function() use ($item, $itemTypeName) : void {
            if(isset($this->deserializers[$itemTypeName])){
                unset($this->deserializers[$itemTypeName]);
            }
            $this->map($itemTypeName, static fn(SavedItemData $_) => clone $item);
        })->call(GlobalItemDataHandlers::getDeserializer());

        (function() use ($item, $itemTypeName) : void {
            $this->itemSerializers[$item->getTypeId()] = static fn() => new SavedItemData($itemTypeName);
        })->call(GlobalItemDataHandlers::getSerializer());

        (function() use ($item, $runtimeId, $itemTypeName) : void {
            $this->stringToIntMap[$itemTypeName] = $runtimeId;
            $this->intToStringIdMap[$runtimeId] = $itemTypeName;
            $this->itemTypes[] = new ItemTypeEntry($itemTypeName, $runtimeId, true);
        })->call(TypeConverter::getInstance()->getItemTypeDictionary());

        if ($namespace !== "") {
            StringToItemParser::getInstance()->register($namespace, fn() => clone $item);
        }

        if ($addCreativeInventory) {
            CreativeInventory::getInstance()->add($item);
        }

        $this->wrapperItems[$item::class] = $item;
    }
}
