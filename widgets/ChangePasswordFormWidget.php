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

use rhosocial\user\forms\ChangePasswordForm;
use yii\base\Widget;

class ChangePasswordFormWidget extends Widget
{
    /**
     * @var ChangePasswordFOrm
     */
    public $model;
    
    public function init()
    {
        if (is_null($this->model) || !($this->model instanceof ChangePasswordForm)) {
            $this->model = new ChangePasswordForm();
        }
    }
    
    public function run()
    {
        return $this->render('change-password-form-widget', ['model' => $this->model]);
    }
}