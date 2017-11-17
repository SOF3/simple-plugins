<?php

/*
 *
 * simple-plugins
 *
 * Copyright (C) 2017 SOFe
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

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
