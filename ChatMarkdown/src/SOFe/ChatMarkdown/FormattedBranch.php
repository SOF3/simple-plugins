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

class FormattedBranch implements FormattedNode{
	/** @var Format|null */
	private $format;
	/** @var FormattedNode[] */
	private $children = [];

	public function __construct(?Format $format, array $children = []){
		$this->format = $format;
		$this->children = $children;
	}

	public function addChild(FormattedNode $node) : void{
		$this->children[] = $node;
	}

	public function toTokens(?Format $context) : array{
		$format = $context !== null ? ($this->format !== null ? $context->add($this->format) : $context) : $this->format;
		$output = [[]];
		foreach($this->children as $child){
			$output[] = $child->toTokens($format);
		}
		return array_merge(...$output);
	}
}
