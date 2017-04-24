<?php

namespace SOFe\JoinDisplays;

use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class PlayerDisplayTask extends PluginTask{
	/** @var DisplaySession[] */
	private $sessions = [];

	public function addSession(DisplaySession $session){
		$this->sessions[$session->getPlayer()->getId()] = $session;
		ksort($session->getExecutions());
	}

	public function onRun($currentTick){
		foreach($this->sessions as $pid => $session){
			if(count($session->getExecutions()) === 0 || !$session->getPlayer()->isOnline()){
				unset($this->sessions[$pid]);
				continue;
			}
			foreach($session->getExecutions() as $tick => $ops){
				if($tick > $currentTick){
					continue 2;
				}
				unset($session->getExecutions()[$tick]);
				foreach($ops as list($verb, $param)){
					$this->executeOperation($session->getPlayer(), $verb, $param);
				}
			}
		}
	}

	private function executeOperation(Player $player, string $verb, $param){
		if($verb === "message"){
			$player->sendMessage(JoinDisplays::preprocess($player, $param));
		}elseif($verb === "tip"){
			$player->sendTip(JoinDisplays::preprocess($player, $param));
		}elseif($verb === "whisper"){
			$player->sendWhisper(JoinDisplays::preprocess($player, $param["sender"]),
				JoinDisplays::preprocess($player, $param["message"]));
		}elseif($verb === "actionbar"){
			$player->addActionBarMessage(JoinDisplays::preprocess($player, $param));
		}elseif($verb === "popup"){
			$player->sendPopup(JoinDisplays::preprocess($player, $param["message"]),
				JoinDisplays::preprocess($player, $param["subtitle"]));
		}elseif($verb === "title"){
			$player->addTitle(JoinDisplays::preprocess($player, $param["title"]),
				JoinDisplays::preprocess($player, $param["subtitle"]),
				$param["fadein"], $param["stay"], $param["fadeout"]);
		}
	}

}
