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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;

class DelayedForms extends PluginBase implements Listener{
	/** @var int */
	private $delay;
	private $except = false;
	private $exceptPlayers = [];

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->delay = (int) ($this->getConfig()->get("delay", 5) * 20);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackPluginTask($this, function(){
			$this->exceptPlayers = [];
		}), 1);
	}

	/**
	 * @param PlayerCommandPreprocessEvent $event
	 * @priority        MONITOR
	 * @ignoreCancelled true
	 */
	public function e_cmdPp(PlayerCommandPreprocessEvent $event){
		$this->exceptPlayers[$event->getPlayer()->getId()] = true;
		// the player won't accidentally click a wrong button if he just sent a command. Delay is unnecessary.
	}

	/**
	 * @param DataPacketReceiveEvent $event
	 * @priority        LOWEST
	 * @ignoreCancelled true
	 */
	public function e_packetRecv(DataPacketReceiveEvent $event){
		$pid = $event->getPacket()::NETWORK_ID;
		if(!$this->except && $pid === ProtocolInfo::MODAL_FORM_REQUEST_PACKET && !isset($this->exceptPlayers[$event->getPlayer()->getId()])){
			$player = $event->getPlayer();
			$packet = $event->getPacket();
			$event->setCancelled();
			$this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackPluginTask($this, function() use ($player, $packet){
				$this->except = true;
				$player->dataPacket($packet);
				$this->except = false;
			}), $this->delay);
		}

		if($pid === ProtocolInfo::MODAL_FORM_RESPONSE_PACKET){
			$this->exceptPlayers[$event->getPlayer()->getId()] = true;
			// the player won't accidentally click a wrong button if he just sent a form. Delay is unnecessary.
		}
	}
}
