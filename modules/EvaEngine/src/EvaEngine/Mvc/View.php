<?php

namespace Eva\EvaEngine\Mvc;

class View extends \Phalcon\Mvc\View
{
    protected $moduleLayout;

    protected $moduleViewsDir;

    protected $moduleLayoutName;

    public function getModuleLayout()
    {
        return $this->moduleLayout;
    }

    public function setModuleLayout($moduleName, $layoutPath)
    {
        $moduleManager = $this->getDI()->get('moduleManager');
        if(!$moduleManager) {
            return $this;
        }
        $this->moduleLayout = $moduleManager->getModulePath($moduleName) . $layoutPath;
        if($this->moduleViewsDir) {
            $this->setRelatedPath();
        }
        return $this;
    }

    public function getModuleViewsDir()
    {
        return $this->moduleViewsDir;
    }

    public function setModuleViewsDir($moduleName, $viewsDir)
    {
        $moduleManager = $this->getDI()->get('moduleManager');
        if(!$moduleManager) {
            return $this;
        }

        $modulePath = $moduleManager->getModulePath($moduleName);
        $this->moduleViewsDir = $moduleViewsDir = $modulePath . $viewsDir;
        $this->setViewsDir($moduleViewsDir);
        if($this->moduleLayout) {
            $this->setRelatedPath();
        }
        return $this;
    }

    protected function setRelatedPath()
    {
        $moduleViewsDir = realpath($this->moduleViewsDir);
        $moduleLayout = realpath(dirname($this->moduleLayout));
        $layoutName = basename($this->moduleLayout);
        $this->setLayoutsDir(DIRECTORY_SEPARATOR . $this->relativePath($moduleViewsDir, $moduleLayout));
        $this->setLayout($layoutName);
        return $this;
    } 

    protected function relativePath($from, $to, $ps = DIRECTORY_SEPARATOR)
    {
        $arFrom = explode($ps, rtrim($from, $ps));
        $arTo = explode($ps, rtrim($to, $ps));
        while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
            array_shift($arFrom);
            array_shift($arTo);
        }
        return str_pad("", count($arFrom) * 3, '..' . $ps) . implode($ps, $arTo);
    }

}
