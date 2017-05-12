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

use rhosocial\user\User;
use yii\web\Model;

/**
 * Class UsernameForm
 * @package rhosocial\user\forms
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UsernameForm extends Model
{
    /**
     * @var User
     */
    public $user;
    public $username;

    /**
     * @return mixed
     */
    protected function getNoInitUsername()
    {
        $class = $this->user->usernameClass;
        return $class::buildNoInitModel();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'string', 'max' => 32, 'min' => '2'],
            ['username', 'match', 'not' => true, 'pattern' => '/^\d+$/', 'message' => Yii::t('user', 'The username can not be a pure number.')],
            ['username', 'match', 'pattern' => '/^\w{2,32}$/'],
            ['username', 'unique', 'targetClass' => $this->user->usernameClass, 'targetAttribute' => $this->getNoInitUsername()->contentAttribute],
        ];
    }

    /**
     * Change username.
     * @return bool
     */
    public function changeUsername()
    {
        if ($this->validate()) {
            $username = $this->user->createUsername($this->username);
            if (!$username) {
                return false;
            }
            return $username->save();
        }
        return false;
    }
}
