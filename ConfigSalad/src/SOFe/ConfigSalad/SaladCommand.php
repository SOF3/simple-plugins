<?php

namespace SOFe\ConfigSalad;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\permission\Permission;
use pocketmine\Player;

class SaladCommand extends Command implements PluginIdentifiableCommand{
	/** @var ConfigSalad */
	private $plugin;
	/** @var string|string[] */
	private $response;

	public function __construct(ConfigSalad $salad, $name, $config){
		parent::__construct($name, $config["description"] ?? "", null, (array) ($config["aliases"] ?? []));
		/** @var string $perm */
		$perm = $config["permission"] ?? ("configsalad." . $name);
		$permission = $salad->getServer()->getPluginManager()->getPermission($perm);
		if($permission === null){
			$parent = $salad->getServer()->getPluginManager()->getPermission("configsalad.commands");
			$permission = new Permission($perm, "Permission to use /$name", Permission::getByName($config["access"] ?? "true"));
			$parent->getChildren()[$perm] = true;
			$salad->getServer()->getPluginManager()->addPermission($permission);
		}
		$this->setPermission($perm);
		$this->plugin = $salad;
		$this->response = is_array($config["response"]) ? implode("\n", $config["response"]) : $config["response"];
	}

	public function execute(CommandSender $sender, $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return false;
		}

		$context = ["server" => $sender->getServer(), "configsalad" => $this->plugin];
		if($sender instanceof Player){
			$context["player"] = $sender;
		}
		return $this->plugin->preprocess(implode("\n", $this->response), $context);
	}

	public function getPlugin(){
		return $this->plugin;
	}
}
