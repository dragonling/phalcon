<?php

namespace Eva\EvaEngine;

class ModuleManager
{
    protected $modules;

    public function getModules()
    {
        return $this->modules;
    }

    public function getModulePath($moduleName)
    {
        $modules = $this->modules;
        if(!isset($modules[$moduleName]['path'])) {
            return false;
        }
        return dirname($modules[$moduleName]['path']);
    }

    public function getModuleConfig($moduleName)
    {
        $modulePath = $this->getModulePath($moduleName);
        if(file_exists($modulePath . '/config/config.php')) {
            return include $modulePath . '/config/config.php';
        }
        return array();
    }

    public function __construct($modules = null)
    {
        $this->modules = $modules;
    }
}
