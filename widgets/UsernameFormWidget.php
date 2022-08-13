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

use rhosocial\user\forms\UsernameForm;
use yii\base\Widget;

/**
 * Class UsernameFormWidget
 * @package rhosocial\user\widgets
 */
class UsernameFormWidget extends Widget
{
    /**
     * @var UsernameForm
     */
    public $model;
    public function run()
    {
        return $this->render('username-form', ['model' => $this->model]);
    }
}
