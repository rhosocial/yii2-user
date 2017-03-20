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

use Yii;
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
    public $first_name;
    public $last_name;
    public $gender = 1;
    public $userClass;
    public $model = null;
    
    public function init()
    {
        if (empty($this->userClass)) {
            $this->userClass = Yii::$app->user->identityClass;
        }
    }
    
    public function rules()
    {
        return [
            [['nickname', 'password', 'password_repeat', 'first_name', 'last_name', 'gender'], 'required'],
            ['nickname', 'string', 'max' => 32],
            [['password', 'password_repeat'], 'string', 'min' => 6, 'max' => 32],
            ['password', 'compare'],
            [['first_name', 'last_name'], 'string', 'max' => 255],
            ['gender', 'in', 'range' => array_keys(\rhosocial\user\Profile::$genders)],
        ];
    }
    
    public function register()
    {
        if ($this->validate()) {
            $class = $this->userClass;
            $user = new $class(['password' => $this->password]);
            /* @var $user \rhosocial\user\User */
            $profile = $user->createProfile(['nickname' => $this->nickname, 'first_name' => $this->first_name, 'last_name' => $this->last_name, 'gender' => $this->gender]);
            $result = $user->register([$profile]);
            if ($result == true) {
                $this->model = $user;
            }
            return $result;
        }
        return false;
    }
}
