<?php

declare(strict_types=1);

namespace SOFe\PersistTips;

use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use function assert;

class SendTipTask extends PluginTask{
	public function onRun(int $currentTick) : void{
		assert($this->owner instanceof PersistTips);

		/**
		 * @var Player $player
		 * @var string $tip
		 */
		foreach($this->owner->tips as $player => $tip){
			$player->sendTip($tip);
		}
	}
}
