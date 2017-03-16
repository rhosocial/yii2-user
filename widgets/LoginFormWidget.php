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

use rhosocial\user\forms\LoginForm;
use yii\base\Widget;

class LoginFormWidget extends Widget
{
    public $model;
    
    public function init()
    {
        if (is_null($this->model) || !($this->model instanceof LoginForm)) {
            $this->model = new LoginForm();
        }
    }
    
    public function run()
    {
        $this->render('login-form-widget', ['model' => $this->model]);
    }
}