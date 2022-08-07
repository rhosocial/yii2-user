<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2022 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\grid;

use Yii;
use yii\helpers\Html;

/**
 * Class ActionColumn
 * @package rhosocial\user\grid
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class ActionColumn extends \yii\grid\ActionColumn
{
    /**
     * @var bool Indicate whether the default buttons use icon or not.
     */
    public $useIcon = true;

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        $this->initDefaultButton('view', $this->useIcon ? 'eye-open' : false);
        $this->initDefaultButton('update', $this->useIcon ? 'pencil' : false);
        $this->initDefaultButton('delete', $this->useIcon ? 'trash' : false, [
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
        ]);
    }

    /**
     * Initializes the default button rendering callback for single button
     * @param string $name Button name as it's written in template
     * @param string $iconName The part of Bootstrap glyphicon class that makes it unique
     * @param array $additionalOptions Array of additional options
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'view':
                        $title = Yii::t('yii', 'View');
                        break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Yii::t('yii', 'Delete');
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                if ($iconName == false) {
                    $icon = $options['title'];
                } else {
                    $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);
                }
                return Html::a($icon, $url, $options);
            };
        }
    }
}
