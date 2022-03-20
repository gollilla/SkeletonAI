<?php

namespace soradore\skeleton\ai;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\world\World;
use pocketmine\entity\Location;
use pocketmine\item\SpawnEgg;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\math\Vector3;
use soradore\skeleton\ai\event\Listener;
use soradore\skeleton\ai\entity\Skeleton;

class main extends PluginBase {

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents(new Listener($this), $this);

        EntityFactory::getInstance()->register(Skeleton::class, function(World $world,CompoundTag $nbt): Skeleton{
            return new Skeleton(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        },['Skeleton', 'minecraft:skeleton'], EntityLegacyIds::SKELETON);

        ItemFactory::getInstance()->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::SKELETON), "Skeleton Spawn Egg") extends SpawnEgg{
			public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
				return new Skeleton(Location::fromObject($pos, $world, $yaw, $pitch));
			}
		});
    }
    
}

