<?php

namespace SOFe\SimplePermissions;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmien\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerJoinEvent,
	PlayerQuitEvent
};
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\PluginBase;

class SimplePermissions extends PluginBase implements Listener{
	private $data;
	private $dataFile;
	private $att = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(!is_dir($this->getDataFolder())) mkdir($this->getDataFolder(), 0777, true);
		$this->data = is_file($this->dataFile = $this->getDataFolder() . "players.json") ? json_decode($this->dataFile, true) : [];
		$this->getServer()->getScheduler()->scheduleDelayedTask(new class($this) extends PluginTask{
			public function onRun($t){
				foreach($this->owner->getServer()->getOnlinePlayers() as $player){
					$this->owner->startSession($player);
				}
			}
		}, 1);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new class($this) extends PluginTask{
			public function onRun($t){
				$this->owner->tick();
				if(($t & 0x1111) === 0x1111) $this->owner->saveData();
			}
		}, 1);
	}

	public function onDisable(){
		foreach($this->getServer()->getOnlinePlayers() as $player){
			$this->endSession($player);
		}
		$this->saveData();
	}

	public function onCommand(CommandSender $issuer, Command $cmd, $l, array $params){
		switch($cmd->getName()){
			case "setperm":
				if(!isset($args[1])) return false;
				$name = array_shift($params);
				$player = $this->getServer()->getPlayer($name);
				if($player === null){
					$issuer->sendMessage("Error: no player with name beginning with $name");
					return true;
				}
				if(strlen($player->getName()) !== $name){
					$issuer->sendMessage("Notice: Interpreting \"$name\" as {$player->getName()}");
				}
				$name = strtolower($player->getName());
				$perm = array_shift($params);
				if($this->getServer()->getPluginManager()->getPermission($perm) === null){
					$issuer->sendMessage("Warning: Permission $perm is not registered");
				}
				$boolStr = strtolower(array_shift($params) ?? "y");
				switch($boolStr{0}){
					case "n":
					case "f":
						$bool = false;
						break;
					case "t":
					case "y":
						$bool = true;
						break;
					default:
						if($boolStr === "on"){
							$bool = true;
							break;
						}
						if($boolStr === "off"){
							$bool = false;
							break;
						}
						$bool = false; // false is probably safer
						$issuer->sendMessage("Warning: Unknown toggle: $boolStr. Interpreting as a \"no\".");
						break;
				}
				$quantity = array_shift($params);
				if($quantity !== null){
					$unit = strtolower(array_shift($args) ?? "h");
					if($unit !== "s" and substr($unit, -1) === "s") $unit = substr($unit, 0, -1);
					static $units = [
						"s" => 1,
						"sec" => 1,
						"second" => 1,
						"i" => 60,
						"min" => 60,
						"minute" => 60,
						"h" => 3600,
						"hr" => 3600,
						"hour" => 3600,
						"d" => 86400,
						"day" => 86400,
						"w" => 604800,
						"wk" => 604800,
						"week" => 604800,
						"m" => 86400 * 30,
						"month" => 86400 * 30,
						"y" => 86400 * 365,
						"yr" => 86400 * 365,
						"year" => 86400 * 365,
					];
					if(!is_numeric($quantity) || !isset($units[$unit])){
						$issuer->sendMessage("Error: Cannot understand time period \"$quantity $unit\"");
						return true;
					}
					$period = $units[$unit] * floatval($quantity);
					$timeout = time() + $period;
					$formatted = (new \DateTime('now', \DateTimeZone::UTC))->setTimestamp($timeout)->format("Y-m-d H:i:s") . " UTC";
					$issuer->sendMessage("Success: Setting $perm of $name to " . ($bool ? "true" : "false") . " until $formatted");
				}else{
					$timeout = PHP_INT_MAX;
					$issuer->sendMessage("Success: Setting $perm of $name to " . ($bool ? "true" : "false") . " forever");
				}

				if(isset($this->data[$name][$perm])){
					$info = $this->data[$name][$perm];
					$boolSign = $info["bool"] ? "+" : "-";
					$till = $info["timeout"] === PHP_INT_MAX ? "forever" : 
						(" until " . ((new \DateTime('now', \DateTimeZone::UTC))->setTimestamp($info["timeout"])->format("Y-m-d H:i:s") . " UTC"));
					$issuer->sendMessage("Notice: Overwritten original permission configuration: {$boolSign}{$perm} $till");
				}

				$this->data[$name][$perm] = ["bool" => $bool, "timeout" => $timeout];
				$this->att($player)->setPermission($perm, $bool);
				return true;
			case "rmperm":
				if(!isset($args[1])) return false;
				list($name, $perm) = $args;
				if($name{0} === "~"){
					$name = substr($name, 1);
					$player = $this->getServer()->getPlayer($name);
					if($player === null){
						$issuer->sendMessage("Error: no player with name beginning with $name");
						return true;
					}
					if(strlen($player->getName()) !== $name){
						$issuer->sendMessage("Notice: Interpreting \"$name\" as {$player->getName()}");
					}
					$name = strtolower($player->getName());
				}else{
					$name = strtolower($name);
				}
				if(!isset($this->data[$name][$perm])){
					$issuer->sendMessage("Error: No permission configuration on $perm for $name");
					return true;
				}
				unset($this->data[$name][$perm]);
				if(isset($player)){
					$this->att($player)->unsetPermission($perm);
					$issuer->sendMessage("Success: Removed $perm from online player {$player->getName()}");
				}else{
					$issuer->sendMessage("Success: Deleted $perm from offline player $name");
				}
				return true;
		}
	}

	public function saveData(){
		file_put_contents($this->dataFile, json_encode($this->data));
	}

	public function e_onJoin(PlayerJoinEvent $event){
		$this->startSession($event->getPlayer());
	}

	public function startSession(Player $player){
		$name = strtolower($player->getName());
		if(isset($this->data[$name])){
			foreach($this->data[$name] as $perm => $info){
				if(time() >= $info["timeout"]){
					unset($this->data[$name][$perm]);
				}else{
					$perms[$perm] = $info["bool"];
				}
			}
			if(isset($perms)){
				$att = $this->att($player);
				$att->setPermissions($perms);
			}
		}
	}

	public function e_onQuit(PlayerQuitEvent $event){
		$this->endSession($event->getPlayer());
	}

	public function endSession(Player $player){
		if(isset($this->att[$player->getId()])){
			$player->removeAttachment($this->att[$player->getId()]);
			unset($this->att[$player->getId()]);
		}
	}

	public function tick(){
		foreach($this->getServer()->getOnlinePlayers() as $player){
			$this->refreshPlayer($player);
		}
	}

	public function refreshPlayer(Player $player){
		$name = strtolower($player->getName());
		if(isset($this->data[$name])){
			foreach($this->data[$name] as $perm => $info){
				if(time() > $info["timeout"]){
					$unsets[$perm] = true;
					unset($this->data[$name][$perm]);
				}
			}
			if(isset($unsets)){
				$this->att($player)->unsetPermissions(array_keys($unsets));
			}
		}
	}

	private function att(Player $player){
		if(!isset($this->att[$player->getId()])){
			return $this->att[$player->getId()] = $player->addAttachment($this);
		}
		return $this->att[$player->getId()];
	}
}
