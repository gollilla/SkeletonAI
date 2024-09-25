<?php

namespace soradore\skeleton\ai;

use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\world\World;
use pocketmine\entity\Location;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\SpawnEgg;
use pocketmine\item\ItemIdentifier;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Stick;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use soradore\skeleton\ai\entity\Skeleton;
use soradore\skeleton\ai\items\ItemManager;

class main extends PluginBase {

    public function onEnable(): void{

        EntityFactory::getInstance()->register(
            Skeleton::class, 
            function(World $world, CompoundTag $nbt): Skeleton {
                return new Skeleton(
                    EntityDataHelper::parseLocation($nbt, $world),
                    $nbt
                );
            },
            ['Skeleton', 'minecraft:skeleton']
        );

        /* VanillaItems::register(
            'spawn_skeleton_egg',
            new class(new ItemIdentifier(ItemTypeIds::newId()), "Skeleton Spawn Egg") extends SpawnEgg{
			    public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
				    return new Skeleton(Location::fromObject($pos, $world, $yaw, $pitch));
			    }
		    }
        ); */

        ItemManager::getInstance()->register(
            new class(new ItemIdentifier(ItemTypeIds::newId()), "Skeleton Spawn Egg") extends SpawnEgg{
			    public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
				    return new Skeleton(Location::fromObject($pos, $world, $yaw, $pitch));
			    }
		    },
            ItemTypeNames::SKELETON_SPAWN_EGG,
            'skeleton_spawn_egg',
            true,
        );
    }
    
}

