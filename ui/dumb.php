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

/* Eregansu autoconfiguration: dumb terminal interface */

class DumbUI extends InstallerUI
{
	protected $inDialog = 0;
	protected $first = true;

	public static function detect()
	{
		return true;
	}

	public function begin()
	{
		$this->inDialog++;
		ob_start();
	}

	public function end()
	{
		if(!$this->inDialog)
		{
			return;
		}
		$this->inDialog--;
		$buf = ob_get_clean();

		if($this->first)
		{
			echo $this->title . "\n";
			echo str_repeat('=', strlen($this->title)) . "\n";
			$this->first = false;
		}
		echo "\n";
		echo $buf;
		echo "\n";
	}
		

	public function prompt($prompt, $default = null, $options = null)
	{
		static $stdin;
	
		while($this->inDialog)
		{
			$this->end();
		}
		if(!$stdin)
		{
			$stdin = fopen('php://stdin', 'r');
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
		echo $prompt;
		flush();
		$line = trim(fgets($stdin));
		if(!strlen($line) && strlen($default))
		{
			$line = $default;
		}
		echo "\n";
		flush();
		return $line;
	}	

	public function notice()
	{
		$args = func_get_args();
		$str = trim(implode(' ', $args));
		echo "--> " . $str . "\n";
	}

	public function error()
	{
		$args = func_get_args();
		$str = trim(implode(' ', $args));
		echo "*** " . $str . "\n";
	}

	public function warning()
	{
		$args = func_get_args();
		$str = trim(implode(' ', $args));
		echo "*** " . $str . "\n";
	}

	public function progress()
	{
		$args = func_get_args();
		$str = trim(implode(' ', $args));
		echo " +> " . $str . "\n";
	}
}

