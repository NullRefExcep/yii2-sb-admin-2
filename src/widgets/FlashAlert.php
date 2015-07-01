<?php

namespace nullref\widgets;

use yii\bootstrap\Alert;
use yii\bootstrap\Widget;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */
class FlashAlert extends Widget
{
    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - $key is the name of the session flash variable
     * - $value is the bootstrap alert type (i.e. danger, success, info, warning)
     */
    public $alertTypes = [
        'error' => 'alert-danger',
        'danger' => 'alert-danger',
        'success' => 'alert-success',
        'info' => 'alert-info',
        'warning' => 'alert-warning'
    ];

    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];

    protected $content = '';

    /**
     * Collect flash messages and collect to into content
     * @throws \Exception
     */
    public function init()
    {
        parent::init();
        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes();
        $appendCss = isset($this->options['class']) ? ' ' . $this->options['class'] : '';
        foreach ($flashes as $type => $data) {
            if (isset($this->alertTypes[$type])) {
                $data = (array)$data;
                foreach ($data as $i => $message) {
                    $this->options['class'] = $this->alertTypes[$type] . $appendCss;
                    $this->options['id'] = $this->getId() . '-' . $type . '-' . $i;
                    $this->content .= Alert::widget([
                        'body' => $message,
                        'closeButton' => $this->closeButton,
                        'options' => $this->options,
                    ]);
                }
                $session->removeFlash($type);
            }
        }
    }

    /**
     * Output collected content
     * @return string
     */
    public function run()
    {
        parent::run();
        return $this->content;
    }

} 