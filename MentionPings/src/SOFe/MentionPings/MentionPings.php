<?php

namespace SOFe\MentionPings;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\level\sound\PopSound;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class MentionPings extends PluginBase implements Listener{
	private static function searchLastToken(string $substr, string $default) : string{
		$bold = false;
		$italic = false;
		foreach(TextFormat::tokenize($substr) as $token){
			if(substr($token, 0, strlen(TextFormat::ESCAPE)) === TextFormat::ESCAPE){
				$default = $token;
				if($token === TextFormat::BOLD){
					$bold = true;
				}elseif($token === TextFormat::ITALIC){
					$italic = true;
				}elseif($token === TextFormat::RESET){
					$bold = $italic = false;
				}
			}
		}
		return ($bold ? TextFormat::BOLD : TextFormat::RESET) . ($italic ? TextFormat::ITALIC : "") . $default;
	}

	private $startSymbol, $endSymbol;

	public function onEnable(){
		$this->saveDefaultConfig();
		$pattern = '/\$\{([A-Z_]+)\}/';
		$callback = function($match){
			$name = $match[1];
			if(defined(TextFormat::class . "::" . strtoupper($name))){
				return constant(TextFormat::class . "::" . strtoupper($name));
			}
			return $match[0];
		};
		$this->startSymbol = preg_replace_callback($pattern, $callback, $this->getConfig()->get("start-symbol", '${YELLOW}${ITALIC}'));
		$this->endSymbol = preg_replace_callback($pattern, $callback, $this->getConfig()->get("end-symbol", '${FT}'));
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @param PlayerChatEvent $event
	 * @priority        HIGHEST
	 * @ignoreCancelled true
	 */
	public function onChat(PlayerChatEvent $event){
		$message = $event->getMessage();
		$recipients = $event->getRecipients();
		foreach($recipients as $i => $recipient){
			$changed = false;
			$name = $recipient->getName();
			while(($pos = stripos($message, $recipient->getName())) !== false){
				$changed = true;
				$subOutput = $this->getServer()->getLanguage()->translateString($event->getFormat(),
					[$event->getPlayer()->getDisplayName(), substr($message, 0, $pos)]);
				$endSymbol = self::searchLastToken($subOutput, TextFormat::WHITE);
				$message = substr_replace(
					substr_replace($message, str_replace('${RT}', $endSymbol, $this->endSymbol), $pos + strlen($name), 0),
					$this->startSymbol, $pos, 0);
			}
			if($changed){
				unset($recipients[$i]);
				$recipient->sendMessage($this->getServer()->getLanguage()->translateString($event->getFormat(),
					[$event->getPlayer()->getDisplayName(), $message]));
				$recipient->getLevel()->addSound(new PopSound($recipient));
			}
		}
		$event->setRecipients($recipients);
	}
}
