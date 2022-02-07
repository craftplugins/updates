<?php

namespace craftplugins\updates;

use Craft;
use craft\events\TemplateEvent;
use craft\web\View;
use yii\base\Event;

class Plugin extends \craft\base\Plugin
{
    public function init()
    {
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\updates\\controllers';
        }

        parent::init();
    }
}
