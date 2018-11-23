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

class Pattern{
	/** @var string */
	private $start;
	/** @var string */
	private $end;
	/** @var boolean */
	private $wholeWord;
	/** @var Format */
	private $then;

	public function __construct(string $start, string $end, bool $wholeWord, Format $then){
		$this->start = $start;
		$this->end = $end;
		$this->wholeWord = $wholeWord;
		$this->then = $then;
	}

	public function getStart() : string{
		return $this->start;
	}

	public function getEnd() : string{
		return $this->end;
	}

	public function isWholeWord() : bool{
		return $this->wholeWord;
	}

	public function getThen() : Format{
		return $this->then;
	}
}
