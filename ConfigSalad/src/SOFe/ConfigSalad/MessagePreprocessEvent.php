<?php

namespace SOFe\ConfigSalad;

use pocketmine\event\plugin\PluginEvent;

class MessagePreprocessEvent extends PluginEvent{
	private $context;
	private $replaces;

	public function __construct(ConfigSalad $salad, array $context, array $defaultReplacement){
		parent::__construct($salad);
		$this->context = $context;
		$this->replaces = $defaultReplacement;
	}

	public function getContext(string $name, $default = null){
		return $this->context[$name] ?? $default;
	}

	public function setReplace(string $key, string $value){
		$this->replaces[$key] = $value;
	}

	public function replaceString(string $input) : string{
		return str_replace(array_keys($this->replaces), array_values($this->replaces), $input);
	}
}
