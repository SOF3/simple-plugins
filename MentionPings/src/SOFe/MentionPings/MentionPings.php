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
	private $rawSubstring;

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
		$this->rawSubstring = $this->getConfig()->get("raw-substring", false);
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
			$name = $recipient->getName();
			$escaped = preg_quote($name, "/");
			$pattern = $this->rawSubstring ? "/$escaped/" : ('/(^|[^A-Za-z0-9_])(' . $escaped . ')($|[^A-Za-z0-9_])/');
			$lastOffset = 0;
			$newMessage = preg_replace_callback($pattern, function($match) use ($event, $message, &$lastOffset){
				list($whole, $front, $name, $back) = $this->rawSubstring ? [$match[0], "", $match[0], ""] : $match;
				$lastOffset = strpos($message, $whole, $lastOffset);
				$subOutput = $this->getServer()->getLanguage()->translateString($event->getFormat(),
					[$event->getPlayer()->getDisplayName(), substr($message, 0, $lastOffset)]);
				$endSymbol = self::searchLastToken($subOutput, TextFormat::WHITE);
				$edit = $front . $this->startSymbol . $name . str_replace('${RT}', $endSymbol, $this->endSymbol) . $back;
				$lastOffset += strlen($edit);
				return $edit;
			}, $message);
			if($message !== $newMessage){
				unset($recipients[$i]);
				$recipient->sendMessage($this->getServer()->getLanguage()->translateString($event->getFormat(),
					[$event->getPlayer()->getDisplayName(), $newMessage]));
				$recipient->getLevel()->addSound(new PopSound($recipient));
			}
		}
		$event->setRecipients($recipients);
	}
}
