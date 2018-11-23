<?php

declare(strict_types=1);

namespace SOFe\ChatMarkdown;

use function mb_strpos;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;
use function mb_strlen;
use function usort;

class ChatMarkdown extends PluginBase implements Listener{
	/** @var Pattern[] */
	private $patterns = [];
	private $useBackSlashes;

	public function onEnable() : void{
		$this->saveDefaultConfig();
		$this->loadConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	private function loadConfig() : void{
		foreach($this->getConfig()->get("patterns") as $pattern){
			$pattern = new Pattern($pattern["start"], $pattern["end"], $pattern["wholeWord"] ?? false, Format::fromString($pattern["then"]));
			$this->patterns[] = $pattern;
		}
		usort($this->patterns, function(Pattern $a, Pattern $b) : int{
			// reverse-sort start length
			if(($r = mb_strlen($a->getStart()) <=> mb_strlen($b->getStart())) !== 0){
				return -$r;
			}
			// reverse-sort end length
			if(($r = mb_strlen($a->getEnd()) <=> mb_strlen($b->getEnd())) !== 0){
				return -$r;
			}
			// give priority to wholeWord patterns
			if(($r = $a->isWholeWord() <=> $b->isWholeWord()) !== 0){
				return -$r;
			}
			return 0;
		});
		$this->useBackSlashes = $this->getConfig()->get("backslash-escape");
	}

	/**
	 * @param PlayerChatEvent $event
	 *
	 * @priority        LOW
	 * @ignoreCancelled true
	 */
	public function e_chat(PlayerChatEvent $event) : void{
		$event->setMessage($this->formatMessage($event->getMessage()));
	}

	public function formatMessage(string $message) : string{
		$tokens = $this->createFormattedTree($message);

	}

	public function createFormattedTree(string $string){
		$reader = new MbStringReader($string);
		$root = new FormattedBranch(null, []);
		$leaf = $root;
		$currentPattern = null;
		while($reader->hasMore()){
			if($reader->consumePrefix($leaf->))
			foreach($this->patterns as $pattern){
				if($reader->consumePrefix($pattern->getStart())){

				}
			}
		}
	}
}
