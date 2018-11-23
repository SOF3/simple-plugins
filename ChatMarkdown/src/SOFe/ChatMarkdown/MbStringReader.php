<?php

/*
 * simple-plugins
 *
 * Copyright (C) 2018 SOFe
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace SOFe\ChatMarkdown;

use function mb_substr;
use function strlen;
use function substr;

class MbStringReader{
	/** @var string */
	private $string;

	public function __construct(string $string){
		$this->string = $string;
	}

	public function startsWith(string $string) : bool{
		/** @noinspection SubStrUsedAsStrPosInspection */
		return substr($this->string, 0, strlen($string)) === $string;
	}

	public function consumePrefix(string $string) : bool{
		$len = strlen($string);
		/** @noinspection SubStrUsedAsStrPosInspection */
		if(substr($this->string, 0, $len) === $string){
			$this->consumeBytes($len);
			return true;
		}
		return false;
	}

	public function consumeBytes(int $bytes) : void{
		$this->string = substr($this->string, $bytes);
	}

	public function readChar() : string{
		$ret = mb_substr($this->string, 0, 1);
		$this->consumeBytes(strlen($ret));
		return $ret;
	}

	public function hasMore() : bool{
		return $this->string !== "";
	}
}
