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

namespace rhosocial\user\forms;

use yii\base\Model;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class RegisterForm extends Model
{
    public $nickname;
    public $password;
    public $password_repeat;
    
    public function rules()
    {
        return [
            ['nickname', 'string', 'max' => 32],
            [['password', 'repeatPassword'], 'string', 'min' => 8, 'max' => 32],
            ['password', 'compare'],
        ];
    }
}
