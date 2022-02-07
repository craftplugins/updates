<?php

namespace craftplugins\updates\controllers;

use Craft;
use craft\console\Controller;
use craft\errors\InvalidPluginException;
use craft\helpers\StringHelper;
use craft\models\Updates;
use yii\console\ExitCode;

class UpdatesController extends Controller
{
    /**
     * @var array|string[]
     */
    protected array $friendlyNames = [
        'copy' => 'Deployment Tools',
        'craft' => 'Craft CMS',
        'embeddedassets' => 'Embed Fields',
        'image-optimize' => 'Image Optimisation',
        'redactor' => 'Rich-text Fields',
        'retour' => 'Redirect Tools',
        'seomatic' => 'SEO Tools',
        'simplemap' => 'Map Fields',
        'super-table' => 'Table Fields',
    ];

    /**
     * @return int
     */
    public function actionListUpdates(): int
    {
        $updateData = Craft::$app->getApi()->getUpdates();
        $updates = new Updates($updateData);

        $this->outputUpdate('craft', Craft::$app->version, $updates->cms->getLatest()->version, $updates->cms->getHasCritical(), $updates->cms->status, $updates->cms->phpConstraint);

        $pluginsService = Craft::$app->getPlugins();

        foreach ($updates->plugins as $pluginHandle => $pluginUpdate) {
            if ($pluginUpdate->getHasReleases()) {
                try {
                    $pluginInfo = $pluginsService->getPluginInfo($pluginHandle);
                } catch (InvalidPluginException $e) {
                    continue;
                }
                if ($pluginInfo['isInstalled']) {
                    $this->outputUpdate($pluginHandle, $pluginInfo['version'], $pluginUpdate->getLatest()->version, $pluginUpdate->getHasCritical(), $pluginUpdate->status, $pluginUpdate->phpConstraint);
                }
            }
        }

        return ExitCode::OK;
    }

    /**
     * @param string $handle
     * @param string $from
     * @param string $to
     * @param bool   $critical
     * @param string $status
     */
    protected function outputUpdate(string $handle, string $from, string $to, bool $critical, string $status): void
    {
        $friendlyName = $this->getFriendlyName($handle);
        $version = "{$from} → {$to}";

        if ($critical) {
            $version .= " critical";
        }

        $this->stdout("Updated {$friendlyName} ({$version})");
        $this->stdout(PHP_EOL);
    }

    /**
     * @param string $handle
     *
     * @return string
     */
    protected function getFriendlyName(string $handle): string
    {
        return $this->friendlyNames[$handle] ?? StringHelper::titleizeForHumans($handle);
    }
}
