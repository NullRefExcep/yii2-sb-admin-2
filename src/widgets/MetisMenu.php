<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace nullref\sbadmin\widgets;

use nullref\sbadmin\assets\MetisMenuAsset;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 *
 */
class MetisMenu extends Menu
{
    public $isCollapsed = false;

    /**
     * @var bool $toogle - if true, when expand one submenu, other will be collapsed as accordeon
     * @see https://github.com/onokumus/metisMenu
     **/
    public $toggle = false;

    /**
     * @var bool $doubleTap doubleTapToGo support
     **/
    public $doubleTap = false;
    /**
     * @var string the template used to render a list of sub-menus.
     * In this template, the token `{items}` will be replaced with the rendered sub-menu items.
     */
    public $submenuTemplate = "\n<ul class='nav nav-second-level'>\n{items}\n</ul>\n";
    public $activeCssClass = 'active';
    /**
     * @var boolean whether to automatically activate items according to whether their route setting
     * matches the currently requested route.
     * @see isItemActive()
     */
    public $activateItems = true;
    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     * The activated parent menu items will also have its CSS classes appended with [[activeCssClass]].
     */
    public $activateParents = true;
    /**
     * @var string $badgeTag
     * This property will be overridden by the `badgeClass` option set in individual menu items via [[items]]
     **/
    public $badgeTag = 'span';
    /**
     * @var string $badgeClass (may be as "label label-success")
     * This property will be overridden by the `badgeClass` option set in individual menu items via [[items]]
     **/
    public $badgeClass = 'badge pull-right';
    /**
     * @var string|bool $iconTag (if not false - allow quick add icon by name, like ('icon'=>'beer'), otherwise you mast full write full icon html in icon attribute - such as
     * 'icon'=>'<i class="fa fa-beer"></i>')
     * This property will be overridden by the `badgeClass` option set in individual menu items via [[items]]
     **/
    public $iconTag = 'i';
    /**
     * @var string $iconPrefix (may be as "fa fa-", "fa fa-2x fa-", "glyphicon glyphicon-" etc/ - For simplify set icon),
     * Used only if iconTag not false
     * This property will be overridden by the `iconPrefix` option set in individual menu items via [[items]]
     **/
    public $iconPrefix = 'fa fa-';
    /**
     * @inheritdoc
     */
    public $linkTemplate = '<a href="{url}" title="{title}">{icon}&nbsp;{label}{badge}</a>';
    /**
     * @inheritdoc
     **/
    public $labelTemplate = '<a href="#" title="{title}">{icon}&nbsp;{label}<span class="fa arrow"></span></a>';
    /**
     * @inheritdoc
     **/
    public $options = ['class' => 'nav'];
    private $_id;
    private $_jsOptions = [];
    /**
     * @var string the prefix to the automatically generated widget IDs.
     * @see getId()
     */
    public static $autoIdPrefix = 'metismenu';

    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->_id = $this->options['id'] = $this->getId();
        }
        parent::init();
    }

    public function run()
    {
        $this->registerScript();
        parent::run();
    }

    /**
     * Renders the content of a menu item.
     * Note that the container and the sub-menus are not rendered here.
     * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
     * @return string the rendering result
     */
    protected function renderItem($item)
    {
        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);
            return strtr($template, [
                '{icon}' => ArrayHelper::getValue($item, 'icon', ''),
                '{url}' => Html::encode(Url::to($item['url'])),
                '{title}' => $item['label'],
                '{label}' => Html::tag('span', $item['label'], [
                    'class' => 'menu-label'
                ]),
                '{badge}' => ArrayHelper::getValue($item, 'badge', ''),
            ]);
        } else {
            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);
            return strtr($template, [
                '{icon}' => ArrayHelper::getValue($item, 'icon', ''),
                '{title}' => $item['label'],
                '{label}' => Html::tag('span', $item['label'], [
                    'class' => 'menu-label'
                ]),
            ]);
        }
    }

    /**
     * Normalizes the [[items]] property to remove invisible items and activate certain items.
     * @param array $items the items to be normalized.
     * @param boolean $active whether there is an active child menu item.
     * @return array the normalized menu items
     */
    protected function normalizeItems($items, &$active)
    {
        foreach ($items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                unset($items[$i]);
                continue;
            }
            if (!isset($item['label'])) {
                $item['label'] = '';
            }
            if (!isset($item['badge'])) {
                $item['badge'] = '';
            } else {
                $badgeTag = ArrayHelper::getValue($item, 'badgeTag', $this->badgeTag);
                $badgeClass = ArrayHelper::getValue($item, 'badgeClass', $this->badgeClass);
                $items[$i]['badge'] = Html::tag($badgeTag, $item['badge'], ['class' => $badgeClass]);
            }
            if (isset($item['icon'])) {
                $iconTag = ArrayHelper::getValue($item, 'iconTag', $this->iconTag);
                if ($iconTag) {
                    $iconPrefix = ArrayHelper::getValue($item, 'iconPrefix', $this->iconPrefix);
                    $items[$i]['icon'] = Html::tag($iconTag, '', ['class' => $iconPrefix . $item['icon']]);
                }
            }
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $items[$i]['label'] = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $hasActiveChild = false;
            if (isset($item['items'])) {
                $items[$i]['items'] = $this->normalizeItems($item['items'], $hasActiveChild);
                if (empty($items[$i]['items']) && $this->hideEmptyItems) {
                    unset($items[$i]['items']);
                    if (!isset($item['url'])) {
                        unset($items[$i]);
                        continue;
                    }
                }
            }
            if (!isset($item['active'])) {
                if ($this->activateParents && $hasActiveChild || $this->activateItems && $this->isItemActive($item)) {
                    $active = $items[$i]['active'] = true;
                } else {
                    $items[$i]['active'] = false;
                }
            } elseif ($item['active']) {
                $active = true;
            }
        }
        return array_values($items);
    }

    protected function registerScript()
    {
        $view = $this->getView();
        MetisMenuAsset::register($view);
        $id = $this->_id;
        $this->_jsOptions['toggle'] = ($this->toggle);
        $this->_jsOptions['doubleTapToGo'] = ($this->doubleTap);
        $opts = Json::encode($this->_jsOptions);
        $view->registerJs("jQuery('#$id').metisMenu($opts)");
    }
}
