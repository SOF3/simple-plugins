<?php

declare(strict_types=1);

namespace SOFe\DeadlyFlight;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\scheduler\PluginTask;

class GroundTask extends PluginTask{
	public function onRun(int $currentTick) : void{
		$damage = (float) $this->getOwner()->getConfig()->get("damage", 1);
		$minTime = (int) $this->getOwner()->getConfig()->get("min-flight-time", 30);
		foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player){
			if(!$player->isOnGround() && $player->getInAirTicks() >= $minTime){
				$player->attack(new EntityDamageEvent($player, EntityDamageEvent::CAUSE_FALL, $damage));
			}
		}
	}
}
