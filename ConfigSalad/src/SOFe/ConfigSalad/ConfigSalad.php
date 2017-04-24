<?php

namespace SOFe\ConfigSalad;

use pocketmine\plugin\PluginBase;

class ConfigSalad extends PluginBase{
	public function onEnable(){
		$this->getLogger()->warning("Plugin not finished");
		if(true) return;
		foreach($this->getConfig()->get("commands", []) as $name => $config){
			$this->getServer()->getCommandMap()->register("configsalad", new SaladCommand($this, $name, $config));
		}
	}

	public function preprocess(string $message, array $context){

	}
}
