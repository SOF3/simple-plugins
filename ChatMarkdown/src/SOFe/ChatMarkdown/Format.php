<?php

declare(strict_types=1);

namespace SOFe\ChatMarkdown;

use pocketmine\utils\TextFormat;
use RuntimeException;
use function strtolower;

class Format{
	/** @var string|null */
	public $color = null;
	public $b = false;
	public $i = false;
	public $u = false;
	public $s = false;

	public static function fromString(string $string) : Format{
		switch(strtolower($string)){
			case "black":
				return Format::create(TextFormat::BLACK);
			case "dark_blue":
				return Format::create(TextFormat::DARK_BLUE);
			case "dark_green":
				return Format::create(TextFormat::DARK_GREEN);
			case "dark_aqua":
				return Format::create(TextFormat::DARK_AQUA);
			case "dark_red":
				return Format::create(TextFormat::DARK_RED);
			case "dark_purple":
				return Format::create(TextFormat::DARK_PURPLE);
			case "gold":
				return Format::create(TextFormat::GOLD);
			case "gray":
				return Format::create(TextFormat::GRAY);
			case "dark_gray":
				return Format::create(TextFormat::DARK_GRAY);
			case "blue":
				return Format::create(TextFormat::BLUE);
			case "green":
				return Format::create(TextFormat::GREEN);
			case "aqua":
				return Format::create(TextFormat::AQUA);
			case "red":
				return Format::create(TextFormat::RED);
			case "light_purple":
				return Format::create(TextFormat::LIGHT_PURPLE);
			case "yellow":
				return Format::create(TextFormat::YELLOW);
			case "white":
				return Format::create(TextFormat::WHITE);
			case "bold":
				return Format::create(null, true);
			case "italic":
				return Format::create(null, false, true);
			case "underline":
				return Format::create(null, false, false, true);
			case "strikethrough":
				return Format::create(null, false, false, false, true);
		}

		throw new RuntimeException("Unknown format $string");
	}

	public static function create(
		?string $color,
		bool $b = false,
		bool $i = false,
		bool $u = false,
		bool $s = false
	) : Format{
		$format = new Format;
		$format->color = $color;
		$format->b = $b;
		$format->i = $i;
		$format->u = $u;
		$format->s = $s;
		return $format;
	}

	private function __construct(){
	}

	public function add(Format $that) : Format{
		$new = new Format;
		$new->color = $that->color;
		$new->b = $this->b || $that->b;
		$new->i = $this->i || $that->i;
		$new->u = $this->u || $that->u;
		$new->s = $this->s || $that->s;
		return $new;
	}

	public function raw() : string{
		$output = "";
		if($this->color !== null){
			$output .= $this->color;
		}
		if($this->b){
			$output .= TextFormat::BOLD;
		}
		if($this->i){
			$output .= TextFormat::ITALIC;
		}
		if($this->u){
			$output .= TextFormat::UNDERLINE;
		}
		if($this->s){
			$output .= TextFormat::STRIKETHROUGH;
		}
		return $output;
	}

	public function transition(?Format $from) : string{
		if($from === null){
			return $this->raw();
		}
		$output = "";
		if(
			$from->b && !$this->b &&
			$from->i && !$this->i &&
			$from->u && !$this->u &&
			$from->s && !$this->s){
			$output .= TextFormat::RESET;
		}
		if($this->color !== null && $this->color !== $from->color){
			$output .= $this->color;
		}
		if(!$from->b && $this->b){
			$output .= TextFormat::BOLD;
		}
		if(!$from->i && $this->i){
			$output .= TextFormat::ITALIC;
		}
		if(!$from->u && $this->u){
			$output .= TextFormat::UNDERLINE;
		}
		if(!$from->s && $this->s){
			$output .= TextFormat::STRIKETHROUGH;
		}
		return $output;
	}
}
