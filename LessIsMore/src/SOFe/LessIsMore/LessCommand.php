<?php

/*
 * simple-plugins
 *
 * Copyright (C) 2018 SOFe
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace SOFe\LessIsMore;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class LessCommand extends Command implements PluginIdentifiableCommand{
	/** @var LessIsMore */
	private $plugin;
	/** @var string[][] */
	private $pages;

	public function __construct(LessIsMore $plugin, string $name, string $description, array $aliases, array $pages){
		parent::__construct($name, $description, "/$name [1 - " . \count($pages) . "]", $aliases);
		$this->plugin = $plugin;
		$this->pages = $pages;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
			return;
		}
		$page = (int) $args[0];
		if($page < 1 || $page > \count($this->pages)){
			$sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
			return;
		}

		$sender->sendMessage(TextFormat::AQUA . "Showing page $page of " . \count($this->pages) . ":");
		foreach($this->pages[$page - 1] as $line){
			$sender->sendMessage($line);
		}
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}
}
