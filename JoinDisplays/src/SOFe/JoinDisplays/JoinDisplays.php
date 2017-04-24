<?php

namespace SOFe\JoinDisplays;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class JoinDisplays extends PluginBase implements Listener{
	/** @var PlayerDisplayTask */
	private $task;

	public static function preprocess(Player $player, $message) : string{
		// TODO implement API here

		if(is_array($message)){
			$output = "";
			foreach($message as $submsg){
				$output .= JoinDisplays::preprocess($player, $submsg) . "\n";
			}
			return $output;
		}
		$map = [
			"@p" => $player->getName(),
			"@i" => $player->getAddress(),
			"@t" => date("Y-m-d H:i:s"),
			"@s" => $player->getServer()->getMotd(),
			"@c" => $player->getName(),
		];
		return str_replace(array_keys($map), array_values($map), $message);
	}

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask($this->task = new PlayerDisplayTask($this), 1);
	}

	public function e_onJoin(PlayerJoinEvent $event){
		$this->task->addSession(new DisplaySession($event->getPlayer(), $this->getConfig()->get("join-operations")));
	}
}
