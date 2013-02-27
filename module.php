<?php

/* Copyright 2010-2011 Mo McRoberts.
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

abstract class ModuleInstaller
{
	protected $installer;
	public $name;
	public $path;
	public $moduleOrder = 1000;
	public $ui;
	public $canBeSole = false;
	public $coexists = false; 
	
	public function __construct($installer, $name, $path)
	{
		$this->installer = $installer;
		$this->ui = $installer->ui;
		if(!strlen($this->name))
		{
			$this->name = $name;
		}
		$this->path = $path;
	}
	
	protected function writePlaceholderDBIri($file, $constant = null, $dbname = null, $dbtype = 'mysql', $options = null)
	{
		if(0 == strlen($constant))
		{
			$constant = strtoupper($this->name . '_DB');
		}
		if(0 == strlen($dbname))
		{
			$dbname = $this->name;
		}
		if(0 != strlen($options))
		{
			$options = '?' . $options;
		}
		fwrite($file, '/* define(\'' . $constant . '\', \'' . $dbtype . '://username:password@localhost/' . $dbname . $options . '\'); */' . "\n");
	}
	
	protected function writeWebRoute($file, $isSole = false, $routeClass = null, $routeFile = 'app.php', $routeName = null, $moduleName = null)
	{
		if(!strlen($routeClass))
		{
			$routeClass = $this->name . 'App';
		}
		if(!strlen($routeName))
		{
			$routeName = $this->name;
		}
		if(!strlen($moduleName))
		{
			$moduleName = $this->name;
		}
		if($isSole)
		{
			fwrite($file, "define('HTTP_MODULE_NAME', '" . $routeName . "');\n");
			fwrite($file, "define('HTTP_MODULE_CLASS_PATH', '" . $routeFile . "');\n");
			fwrite($file, "define('HTTP_MODULE_CLASS', '" . $routeClass . "');\n");
		}
		else
		{
			fwrite($file, "\$HTTP_ROUTES['" . $routeName . "'] = array('name' => '" . $routeName . "', 'file' => '" . $routeFile . "', 'class' => '" . $routeClass . "', 'adjustBase' => true);\n");
		}
	}
	
	protected function writeModuleSchema($file, $class = null, $filename = null)
	{
		if(!strlen($class) && !strlen($filename))
		{
			fwrite($file, "\$SETUP_MODULES[] = '" . $this->name . "';\n");
			return;
		}
		if(!strlen($class))
		{
			$class = $this->name . 'Schema';
		}
		if(!strlen($filename))
		{
			$filename = 'schema.php';
		}
		fwrite($file, "\$SETUP_MODULES[] = array('name' => '" . $this->name . "', 'class' => '" . $class . "', 'filename' => '" . $filename . "');\n");
	}
	
	public function writeAppConfig($file, $isSoleWebModule = false, $chosenSoleWebModule = null)
	{
	}
	
	public function writeInstanceConfig($file)
	{
	}
	
	public function canBeSoleWebModule()
	{
		return $this->canBeSole;
	}
	
	public function canCoexistWithSoleWebModule()
	{
		return $this->coexists;
	}
	
	public function createLinks()
	{
		$this->linkTemplates();
	}
	
	protected function linkTemplates($subdir = 'templates', $target = null, $rpath = null)
	{
		if(!strlen($target))
		{
			$target = $this->name;
		}
		if(!strlen($rpath))
		{
			if(substr($this->installer->relModulesPath, 0, 1) == '/')
			{
				$rpath = $this->installer->relModulesPath;
			}
			else
			{
				$rpath = '../../' . $this->installer->relModulesPath;
			}
		}
		if(substr($rpath, -1) != '/') $rpath .= '/';
		$rpath .= $this->name . '/' . $subdir;
		$path = PUBLIC_ROOT . (defined('TEMPLATES_PATH') ? TEMPLATES_PATH : 'templates') . '/';
		if(file_exists($path) && file_exists($this->path . $subdir))
		{
			if(file_exists($path . $target))
			{
				$this->ui->progress("Leaving existing file at " . $path . $target . " in place");
			}
			else
			{
				$this->ui->progress("Linking $rpath to $target in $path");
				@unlink($path . $target);
				symlink($rpath, $path . $target);
			}
		}
	}
}

abstract class BuiltinModuleInstaller extends ModuleInstaller
{
	protected function linkTemplates($subdir = 'templates', $target = null, $rpath = null)
	{
		if(!strlen($rpath))
		{
			if(substr($this->installer->relPlatformPath, 0, 1) == '/')
			{
				$rpath = $this->installer->relPlatformPath;
			}
			else
			{
				$rpath = '../../' . $this->installer->relPlatformPath;
			}
		}
		return parent::linkTemplates($subdir, $target, $rpath);
	}
}
