<?php

namespace soradore\skeleton\ai\entity;

use pocketmine\entity\Living;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class Skeleton extends Living {

    private $target = null;
    private $isNeutral = true;

    private $speed = 0.28;
    private $coolTime = 0;
    private $attkTime = 0;

    public static function getNetworkTypeId() : string{ return EntityIds::SKELETON; }

    protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(1.8, 0.6); //TODO: eye height ??
	}
    
    public function getName() : string{
	    return "Skeleton";
    }

    public function findClosestPlayer(int $distance) : ?Player {
        $result = null;
        foreach ($this->getWorld()->getPlayers() as $player) {
            //[$playerとこのエンティティの距離 < 前の$playerの距離]なら、$resultに$playerを代入
            if ($player->location->distance($this->location) < $distance) {
                $result = $player;//結果に代入
                $distance = $player->location->distance($this->location);//距離を更新
            }
        }

        return $result;
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $world = $this->getWorld();
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $this->attkTime -= $tickDiff;
        $this->coolTime -= $tickDiff;

        //$this->setNameTag("attk: " . $this->attkTime);
        if($this->attkTime > 0)
            return false;
        else
            $this->attkTime = 0;
        
        if($this->getTarget() == null) {
            if ($this->isNeutral) return $hasUpdate;//中立の状態なら処理を終了

            $preTarget = $this->findClosestPlayer(10);
            if ($preTarget === null) {
                $this->isNeutral = true;//中立状態に設定
                return $hasUpdate;//プレイヤーが近くにいなければ処理を終了
            } else {
                $this->isNeutral = false;//中立状態を解除
                $this->target = $preTarget;
            }
        }

        $target = $this->getTarget();
        if(!($target instanceof Player))
            return $hasUpdate;
        
        $speed = $this->getSpeed();
        $this->lookAt($target->location);

        if($this->location->distance($target->location) <= 1){
            if($this->coolTime < 0){
                $ev = new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 3);;
                $target->attack($ev);
                $this->coolTime = 23;
            }
            return $hasUpdate;
        } else if ($this->location->distance($target->location) >= 5) {//5ブロックより遠ければ
            $preTarget = $this->findClosestPlayer(10);//10ブロック以内の一番近いプレイヤーを取得
            if ($preTarget === null) {//プレイヤーが近くにいなければ
                $this->target = null;//ターゲットを空にして、処理をやめる。
                return $hasUpdate;
            } else {//プレイヤーが存在すれば
                $this->target = $preTarget;//ターゲットを設定
            }
        }
            

        $moveX = sin(-deg2rad($this->location->yaw)) * $speed;
        $moveZ = cos(-deg2rad($this->location->yaw)) * $speed;
        $this->checkFront();
        $this->motion->x = $moveX;
        $this->motion->z = $moveZ;

        return $hasUpdate;
    }


    public function attack(EntityDamageEvent $source): void
    {
        if($source instanceof EntityDamageByEntityEvent){
            $source->setKnockBack(0.5);
            $damager = $source->getDamager();
            if($damager instanceof Player) {
                if(!$this->hasTarget()) {
                    $this->setTarget($damager);
                }
            }
        }
        parent::attack($source);
        if($this->attkTime <= 0) {
            $this->attkTime = 15;
        }
    }


    public function jump(): void
    {
        if($this->onGround)
            $this->motion->y = 0.5;
    }


    public function checkFront(): void
    {
        $dv = $this->getDirectionVector()->multiply(1);
        $checkPos = $this->location->add($dv->x, 0, $dv->z)->floor();
        if($this->getWorld()->getBlockAt($checkPos->x, $this->location->y+1, $checkPos->z)->isSolid())
        {
            return;
        }
        if($this->getWorld()->getBlockAt($checkPos->x, $this->location->y, $checkPos->z)->isSolid())
        {
            $this->jump();
        }
    }


    public function setTarget(Player $player)
    {
        $this->isNeutral = false;
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
