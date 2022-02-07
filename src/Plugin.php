<?php

namespace craftplugins\updates;

use Craft;

class Plugin extends \craft\base\Plugin
{
    public function init()
    {
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'craftplugins\\updates\\controllers';
        }

        parent::init();
    }
}
