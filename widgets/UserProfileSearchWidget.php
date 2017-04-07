<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2017 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UserProfileSearchWidget extends Widget
{
    public $formId = 'user-profile-search-form';
    public $formConfig = null;
    public $model;
    public function init()
    {
        if ($this->model == null) {
            throw new InvalidConfigException("The search model should not be empty.");
        }
        if ($this->formConfig == null) {
            $this->formConfig = [
                'id' => $this->formId,
                'action' => ['index'],
                'method' => 'post',
            ];
        }
    }

    public function run()
    {
        return $this->render('user-profile-search', [
            'model' => $this->model,
            'formId' => $this->formId,
            'formConfig' => $this->formConfig
        ]);
    }
}
