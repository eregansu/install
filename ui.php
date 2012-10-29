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

/* Eregansu autoconfiguration: base class for user interfaces */

abstract class InstallerUI
{
	public $title = 'Installer';
	
	protected static $classes = array('readline', 'dumb');

	public static function getInstance()
	{
		foreach(self::$classes as $class)
		{			
			require_once(dirname(__FILE__) . '/ui/' . $class . '.php');
			$class .= 'UI';
			if(call_user_func(array($class, 'detect')))
			{
				return new $class();
			}
		}
		trigger_error('No suitable UI implementation found', E_USER_ERROR);
	}

	protected function __construct()
	{
	}

	abstract public function begin();
	abstract public function end();
	abstract public function prompt($prompt, $default = null, $options = null);
	abstract public function notice();
	abstract public function error();
	abstract public function warning();
	abstract public function progress();
}
