<?php

namespace soradore\skeleton\ai;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use soradore\skeleton\ai\event\Listener;
use soradore\skeleton\ai\entity\Skeleton;

class main extends PluginBase {

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents(new Listener($this), $this);
        Entity::registerEntity(Skeleton::class, false, ['Skeleton', 'minecraft:skeleton']);
    }
    
}

