<?php

/* Copyright 2012 Mo McRoberts.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/* Eregansu autoconfiguration: readline terminal interface */

require_once(dirname(__FILE__) . '/dumb.php');

class ReadlineUI extends DumbUI
{
	public static function detect()
	{
		if(function_exists('readline'))
		{
			echo "Readline is available.\n";
			return true;
		}
		return false;
	}

	public function prompt($prompt, $default = null, $options = null)
	{
		static $stdin;
	
		while($this->inDialog)
		{
			$this->end();
		}
		if(strlen($options))
		{
			$prompt .= ' (' . $options . ')';
		}
		$prompt .= '?';
		if(strlen($default))
		{
			$prompt .= ' [' . $default . '] ';
		}
		$line = trim(readline($prompt));
		if(!strlen($line) && strlen($default))
		{
			$line = $default;
		}
		if(strlen($line))
		{
			readline_add_history($line);
		}
		echo "\n";
		flush();
		return $line;
	}	
}
