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

namespace rhosocial\user\widgets;

use rhosocial\user\forms\LoginForm;
use Yii;
use yii\base\Widget;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class LoginFormWidget extends Widget
{
    /**
     * @var LoginForm
     */
    public $model;

    /**
     * @var string
     */
    public $tip;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_null($this->model) || !($this->model instanceof LoginForm)) {
            $this->model = new LoginForm();
        }
        parent::init();
    }

    /**
     * Run action.
     * @return string
     */
    public function run()
    {
        return $this->render('login-form', [
            'tip' => $this->tip,
            'model' => $this->model
        ]);
    }
}
