<?php

namespace SOFe\JoinDisplays;

use pocketmine\Player;

class DisplaySession{
	/** @var Player */
	private $player;
	/** @var array[] */
	private $executions = [];

	public function __construct(Player $player, array $config){
		$this->player = $player;
		$ticks = $player->getServer()->getTick();
		foreach($config as $i => $operation){
			if(!is_array($operation)){
				throw new \RuntimeException("Error parsing config: More than one operation in operation #" . ($i + 1));
			}
			$operation = array_change_key_case($operation, CASE_LOWER);
			foreach($operation as $verb => $param){
				if($verb === "delay"){
					$ticks += (int) (floatval($param) * 20);
				}elseif($verb === "subtitle" || $verb === "fadein" || $verb === "stay" || $verb === "fadeout"){
					continue;
				}elseif($verb === "message" || $verb === "tip" || $verb === "actionbar"){
					$this->executions[$ticks][] = [$verb, $param];
				}elseif($verb === "popup"){
					$this->executions[$ticks][] = [$verb, [
						"message" => $param,
						"subtitle" => $operation["subtitle"] ?? "",
					]];
				}elseif($verb === "whisper"){
					$this->executions[$ticks][] = [$verb, [
						"sender" => $operation["sender"] ?? "",
						"message" => $param
					]];
				}elseif($verb === "title"){
					$this->executions[$ticks][] = [$verb, [
						"title" => $param,
						"subtitle" => $operation["subtitle"] ?? "",
						"fadein" => (int) ($operation["fadein"] ?? -1),
						"stay" => (int) ($operation["stay"] ?? -1),
						"fadeout" => (int) ($operation["fadeout"] ?? -1),
					]];
				}
			}
		}
	}

	public function getPlayer(){
		return $this->player;
	}

	public function &getExecutions(){
		return $this->executions;
	}
}
