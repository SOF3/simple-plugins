<?php

namespace EvalPlugin;

use pocketmine\command as c;
use pocketmine\plugin\PluginBase as PB;

class EvalPlugin extends PB{
    public function onCommand(c\CommandSender $s, c\Command $c, string $l, array $a):bool{
//        if(!($s instanceof c\ConsoleCommandSender)) return false;
        $code = implode(" ", $a);
        $this->getLogger()->alert("Executing PHP: $code");
        eval($code);
        return true;
    }
}
