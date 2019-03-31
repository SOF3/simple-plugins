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

use jojoe77777\FormAPI\ModalForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class LessCommand extends Command implements PluginIdentifiableCommand{
	/** @var LessIsMore */
	private $plugin;
	/** @var string[][] */
	private $pages;
	/** @var bool */
	private $preferForms;

	public function __construct(LessIsMore $plugin, string $name, string $description, array $aliases, array $pages, bool $preferForms){
		parent::__construct($name, $description, "/$name [1 - " . \count($pages) . "]", $aliases);
		$parent = $plugin->getServer()->getPluginManager()->getPermission("lessismore");
		\assert($parent !== null);
		$permission = new Permission("lessismore.$name", "Permission to use /$name");
		$parent->getChildren()[$permission->getName()] =true;
		$plugin->getServer()->getPluginManager()->addPermission($permission);
		$this->setPermission("lessismore.$name");
		$this->plugin = $plugin;
		$this->pages = $pages;
		$this->preferForms = $preferForms;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		$page = isset($args[0]) ? (int) $args[0] : 1;
		if($page < 1 || $page > \count($this->pages)){
			$sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
			return;
		}

		$this->send($sender, $page);
	}

	private function send(CommandSender $sender, int $page){
		$title = TextFormat::AQUA . "Showing page $page of " . \count($this->pages) . ":";
		$content = [];
		foreach($this->pages[$page - 1] as $line){
			$content[] = $line;
		}

		if($this->preferForms && $sender instanceof Player){
			$form = new ModalForm();
			$form->setTitle($title);
			$form->setContent(implode("\n", $content));
			$form->setButton1($page === 1 ? "Close" : "Previous");
			$form->setButton2($page === \count($this->pages) ? "Close" : "Next");
			$form->setCallable(function(?bool $next) use($sender, $page) : void{
				if($next === null) return;
				if($next && $page === \count($this->pages)) return;
				if(!$next && $page === 1) return;
				$nextPage = $page + ($next ? 1 : -1);
				$this->send($sender, $nextPage);
			});
			$form->sendToPlayer($sender);
		}else{
			$sender->sendMessage($title);
			foreach($content as $line){
				$sender->sendMessage($line);
			}
		}
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}
}
