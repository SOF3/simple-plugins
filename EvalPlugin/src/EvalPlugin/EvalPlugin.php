<?php

namespace EvalPlugin;

use pocketmine\command as c;
use pocketmine\plugin\PluginBase as PB;

class EvalPlugin extends PB{
    public function onCommand(c\CommandSener $s, c\Command $c, $l, array $a){
//        if(!($s instanceof c\ConsoleCommandSender)) return false;
        $code = implode(" ", $args);
        $this->getLogger()->alert("Executing PHP: $code");
        eval($code);
    }
}
