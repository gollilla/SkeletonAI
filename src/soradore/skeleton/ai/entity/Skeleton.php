<?php

namespace soradore\skeleton\ai\entity;

use pocketmine\level\Level;
use pocketmine\entity\Monster;
use pocketmine\entity\Entity;
use pocketmine\PLayer;
use pocketmine\math\Vector3;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class Skeleton extends Monster {

    public const NETWORK_ID = self::SKELETON;

    private $target = null;
    private $speed = 0.28;

    public $width = 0.6;
    public $height = 1.9;
    
    public function getName() : string{
	return "Skeleton";
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $level = $this->getLevel();
        $time = $level->getTimeOfDay();
        if(0 <= $time && $time < Level::TIME_NIGHT){
            $this->kill();
        }
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $this->attackTime -= $tickDiff;

        if($this->attackTime > 0)
            return false;
        else
            $this->attackTime = 0;
        
        if($this->getTarget() == NULL)
            return $hasUpdate;

        $target = $this->getTarget();
        if(!($target instanceof Player))
            return $hasUpdate;
        
        $speed = $this->getSpeed();
        $this->lookAt($target);

        if($this->distance($target) < 1)
            return $hasUpdate;

        $moveX = sin(-deg2rad($this->yaw)) * $speed;
        $moveZ = cos(-deg2rad($this->yaw)) * $speed;
        $this->checkFront();
        $this->motion->x = $moveX;
        $this->motion->z = $moveZ;

        return true;
    }


    public function attack(EntityDamageEvent $source): void
    {
        if($source instanceof EntityDamageByEntityEvent)
            $source->setKnockBack(0.5);
        parent::attack($source);
        $this->attackTime = 17;
        
    }


    public function jump(): void
    {
        if($this->onGround)
            $this->motion->y = 0.5;
    }


    public function checkFront(): void
    {
        $dv = $this->getDirectionVector()->multiply(1);
        $checkPos = $this->add($dv->x, 0, $dv->z)->floor();
        if($this->level->getBlockAt($checkPos->x, $this->y+1, $checkPos->z)->isSolid())
        {
            return;
        }
        if($this->level->getBlockAt($checkPos->x, $this->y, $checkPos->z)->isSolid())
        {
            $this->jump();
        }
    }


    public function setTarget(Player $player)
    {
        $this->target = $player;
    }


    public function getTarget()
    {
        return $this->target;
    }


    public function getSpeed(): float
    {
        return $this->speed;
    }

    
    public function hasTarget(){
        return !is_null($this->getTarget());
    }

    public function getXpDropAmount() : int{
	return 0;
    }

}
