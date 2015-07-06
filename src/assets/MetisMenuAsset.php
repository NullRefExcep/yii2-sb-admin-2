<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\sbadmin\assets;

use yii\web\AssetBundle;

class MetisMenuAsset extends AssetBundle
{
    public $sourcePath = '@bower/metisMenu/dist';
    public $baseUrl = '@web';

    public $css = [
        'metisMenu.css',
    ];

    public $js = [
        'metisMenu.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];

} 