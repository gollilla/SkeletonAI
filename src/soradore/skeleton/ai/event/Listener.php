<?php

namespace soradore\skeleton\ai\event;

use pocketmine\event\Listener as EventListener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\entity\Entity;
use soradore\skeleton\ai\entity\Skeleton;
use pocketmine\player\Player;

class Listener implements EventListener {

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    /*public function onJoin(PlayerJoinEvent $ev){
        $player = $ev->getPlayer();
        $nbt = Entity::createBaseNBT($player);
        $skeleton = Entity::createEntity("Skeleton", $player->getLevel(), $nbt);
        $skeleton->spawnToAll();
    }*/

    public function onDamage(EntityDamageEvent $ev)
    {
       //
    }
}
