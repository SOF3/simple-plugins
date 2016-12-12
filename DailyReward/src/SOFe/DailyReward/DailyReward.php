<?php

namespace SOFe\DailyReward;

use pocketmine\Player;
use pocketmine\comamnd\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class DailyReward extends PluginBase implements Listener{
  public function onEnable(){
    $this->saveDefaultConfig();
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

  public function onJoin(PlayerJoinEvent $event){
    $path = $this->getDataFolder() . strtolower($event->getPlayer()->getName()) . ".json";
    if(!is_file($path)){
      $data = (object) [
        "lastJoin" => 0,
        "timezone" => 0, // can be changed via config
      ];
    }else{
      $data = json_decode(file_get_contents($path));
      if(!is_object($data)){
        $data = (object) [
          "lastJoin" => 0,
          "timezone" => 0, // can be changed via config
        ];
      }
    }
    $timeNow = time() + $data->timezone;
    $timeLast = $data->lastJoin + $data->timezone;
    $daysDiff = ((int) ($timeNow / 86400)) - ((int) $timeLast / 86400);
    
    $data->lastJoin = time();
    file_put_contents($path, json_encode($data));
    
    $action = $this->getConfig()->get("actions")[$daysDiff] ?? [];
    if($action !== []){
      $this->dispatchAction($action, $event->getPlayer());
    }
  }
  
  public function dispatchAction(array $action, Player $player){
    $type = $action["type"]; // not gonna validate the config
    if($type === "command"){
      $this->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("@p", $player->getName(), $action["command"]));
    }else{
      throw new \RuntimeException("Unsupported type $type");
    }
  }
}
