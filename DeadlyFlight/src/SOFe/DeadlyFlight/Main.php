<?php

declare(strict_types=1);

namespace SOFe\DeadlyFlight;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {
	public function onEnable() : void{
		$this->saveDefaultConfig();
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new GroundTask($this), (int) $this->getConfig()->get("frequency", 10));
	}
}
