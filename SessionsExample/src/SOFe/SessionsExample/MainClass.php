<?php

namespace SessionsExample;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;

class MainClass extends PluginBase implements Listener{
    private $playerData = [];

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->playerData[$player->getId()] = $data = new PlayerData($this, $player->getName());
        $data->incrementJoins();
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        // remember to check this! If player quits before he joins the server, or
        // if he is banned/whitelisted/server full etc., PlayerQuitEvent will fire without PlayerJoinEvent first!
        if(isset($this->playerData[$player->getId()])){
            $data = $this->playerData[$player->getId()];
            $data->updateLastOnline();
            $data->save();
            unset($this->playerData[$player->getId()]);
        }
    }
}
