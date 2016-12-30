<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nullref\sbadmin\widgets;

use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Menu as BaseMenu;


class Menu extends BaseMenu
{
    public $isCollapsed = false;

    /**
     * Renders the menu.
     */
    public function run()
    {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        $items = $this->normalizeItems($this->items, $hasActiveChild);
        if (!empty($items)) {
            $view = $this->getView();
            $view->registerJs(<<<JS
            var isCollapse = function() {
                if (jQuery('.sidebar').hasClass('closed')) {
                    jQuery('#page-wrapper').toggleClass('maximized');
                }
            };
            isCollapse();
            jQuery('.menu-button').click(function(){
                var sidebar = jQuery('.sidebar');
                var page = jQuery('#page-wrapper');
                if (sidebar.hasClass('closed')) {
                    sidebar.toggleClass('closed');
                    page.toggleClass('maximized');
                } else {
                    sidebar.toggleClass('closed');
                    page.toggleClass('maximized');
                }
            });
JS
            );

            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'ul');
            $itemsList = Html::tag($tag, $this->renderItems($items), $options);

            $button = Html::button(
                    Html::tag('span', FA::icon(FA::_ARROW_CIRCLE_LEFT)->size(FA::SIZE_2X), [
                        'class' => 'close-arrow'
                    ]) .
                    Html::tag('span', FA::icon(FA::_ARROW_CIRCLE_RIGHT)->size(FA::SIZE_2X), [
                        'class' => 'open-arrow'
                    ]), [
                    'class' => 'btn btn-default menu-button'
                ]) . Html::tag('div', '', ['class' => 'clearfix']);

            $menuWrapper = Html::tag('div', $itemsList, [
                'class' => 'sidebar-nav navbar-collapse',
            ]);

            $menu = Html::tag('div', $button . $menuWrapper, [
                'class' => ($this->isCollapsed) ? 'navbar-default sidebar closed' : 'navbar-default sidebar',
                'role' => 'navigation'
            ]);

            echo $menu;
        }
    }
}