<?php

declare(strict_types=1);

namespace SOFe\PersistTips;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use SplObjectStorage;

class PersistTips extends PluginBase implements Listener{
	/** @var SplObjectStorage|string[] */
	public $tips;

	public function onEnable() : void{
		$this->tips = new SplObjectStorage();
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new SendTipTask($this), 20); // 20 ticks = 1 second, depends on how long the tip stays by default. I don't have a client, so I can't measure it.
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	// clear the player's data when he quits to prevent memory leak or tip retention over sessions
	public function e_quit(PlayerQuitEvent $event) : void{
		if(isset($this->tips[$event->getPlayer()])){
			unset($this->tips[$event->getPlayer()]);
		}
	}

	// Example: set the persistent tip to the player's last chat message

	/**
	 * @param PlayerChatEvent $event
	 * @priority        MONITOR
	 * @ignoreCancelled true
	 */
	public function e_chat(PlayerChatEvent $event) : void{
		$this->tips[$event->getPlayer()] = $event->getMessage();
	}
}
