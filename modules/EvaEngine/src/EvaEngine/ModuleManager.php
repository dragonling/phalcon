<?php

namespace Eva\EvaEngine;

class ModuleManager
{
    protected $modules = array();

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

    public function getModuleRoutesFrontend($moduleName)
    {
        if(isset($this->modules[$moduleName]['routesFrontend'])) {
            if(false === $this->modules[$moduleName]['routesFrontend']) {
                return array();
            }
            
            if(true === file_exists($this->modules[$moduleName]['routesFrontend'])) {
                return include $this->modules[$moduleName]['routesFrontend'];
            } else {
                return array();
            }
        }

        $modulePath = $this->getModulePath($moduleName);
        if(file_exists($modulePath . '/config/routes.frontend.php')) {
            return include $modulePath . '/config/routes.frontend.php';
        }
        return array();
    }

    public function getModuleRoutesBackend($moduleName)
    {
        if(isset($this->modules[$moduleName]['routesBackend'])) {
            if(false === $this->modules[$moduleName]['routesBackend']) {
                return array();
            }
            
            if(true === file_exists($this->modules[$moduleName]['routesBackend'])) {
                return include $this->modules[$moduleName]['routesBackend'];
            } else {
                return array();
            }
        }

        $modulePath = $this->getModulePath($moduleName);
        if(file_exists($modulePath . '/config/routes.backend.php')) {
            return include $modulePath . '/config/routes.backend.php';
        }
        return array();
    }

    public function __construct($modules = null)
    {
        $this->modules = $modules;
    }
}
