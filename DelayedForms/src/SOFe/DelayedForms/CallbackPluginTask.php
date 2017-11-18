<?php

declare(strict_types=1);

namespace SOFe\DelayedForms;

use pocketmine\scheduler\PluginTask;

class CallbackPluginTask extends PluginTask{
	/** @var callable */
	private $callback;

	public function __construct(DelayedForms $owner, callable $callback){
		parent::__construct($owner);
		$this->callback = $callback;
	}

	public function onRun(int $currentTick) : void{
		($this->callback)();
	}
}
