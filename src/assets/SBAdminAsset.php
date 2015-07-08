<?php

namespace nullref\sbadmin\assets;

use yii\web\AssetBundle;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SBAdminAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $baseUrl = '@web';

    public $css = [
        'startbootstrap-sb-admin-2/dist/css/sb-admin-2.css',
        'startbootstrap-sb-admin-2/dist/css/timeline.css',
    ];

    public $js = [
        'startbootstrap-sb-admin-2/dist/js/sb-admin-2.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
        'nullref\sbadmin\assets\MetisMenuAsset'
    ];
} 