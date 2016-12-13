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
        "cnscDays" => 0,
        "timezone" => 0, // can be changed via config
      ];
    }else{
      $data = json_decode(file_get_contents($path));
      if(!is_object($data)){
        $data = (object) [
          "lastJoin" => 0,
          "cnscDays" => 0,
          "timezone" => 0, // can be changed via config
        ];
      }
    }
    $timeNow = time() + $data->timezone;
    $timeLast = $data->lastJoin + $data->timezone;
    $offlineDays = ((int) ($timeNow / 86400)) - ((int) $timeLast / 86400) - 1;
    if($offlineDays === -1){
      return; // no need to modify lastJoin since it is the same day
    }elseif($offlineDays > 0){
      $data->cnscDays = 1;
    }else{
      $data->cnscDays++;
    }
    $data->lastJoin = time();
    file_put_contents($path, json_encode($data));
    
    $this->dispatchActions(Player $player, $data->cnscDays, $offlineDays);
  }
  
  public function dispatchActions(Player $p, int $d, int $o){
    foreach($this->getConfig()->get("actions") as $num => $action){
      $num++;
      if(!isset($action["days"])){
        $this->getLogger()->warning("Skipping action #$num without \"days\" specified");
        continue;
      }
      if($this->matchesRange($action["days"], $d)){
        $this->executeAction($action, $p, $d, $o);
      }
    }
  }
  
  public function matchesRange(string $range, int $d) : bool{
    $multiple = false;
    if($range{strlen($range) - 1} === "n"){
      $multiple = true;
      $range = substr($range, 0, -1);
    }
    $parts = explode("-", $range);
    $from = $parts[0];
    $to = $parts[1] ?? $from;
    
    if($from === "" and $to === ""){
      $this->getLogger()->warning("Invalid range \"$range\"");
      return false;
    }
    
    if($from === ""){
      $from = 1;
    }else{
      if(!is_numeric($from)){
        $this->getLogger()->warning("Invalid range minimum \"$from\"");
        return false;
      }
      $from = (int) $from;
      if($from < 1){
        $this->getLogger()->warning("Invalid range minimum \"$from\": should be a positive integer");
        return false;
      }
    }
    
    if($to === ""){
      $to = PHP_INT_MAX;
    }else{
      if(!is_numeric($to)){
        $this->getLogger()->warning("Invalid range maximum \"$to\"");
        return false;
      }
      $to = (int) $to;
      if($to < $from){
        $this->getLogger()->warning("Invalid range maximum \"$to\": should be greater than range minimum");
        return false;
      }
    }
    
    if($multiple){
      if($from !== $to){
        $this->getLogger()->warning("It is unreasonable to check the multiples of a range! ({$from}-{$to}n)");
      }
      return ($d % $from) === 0;
    }else{
      return $from <= $d and $d <= $to;
    }
  }
  
  public function executeAction(array $action, Player $p, int $d, int $o){
    if(!isset($action["type"])){
      $this->getLogger()->warning("Missing action type");
      return;
    }
    $type = $action["type"];
    if($type === "command"){
      if(!isset($action["commands"])) $this->getLogger()->warning("Missing property \"commands\" for command action");
      $commands = $action["commands"];
      if(!is_array($commands)) $commands = [$commands];
      
      foreach($commands as $command){
        $line = str_replace(["@p", "@i", "@d", "@o"], [$p->getName(), $p->getAddress(), $d, $o], $command);
        $this->getServer()->dispatchCommand($line);
      }
    }else{
      $this->getLogger()->warning("Unknown action type \"$type\"");
    }
  }
}
