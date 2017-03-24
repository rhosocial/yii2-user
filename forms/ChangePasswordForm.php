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
use yii\base\Model;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class ChangePasswordForm extends Model
{
    public $password;
    public $new_password;
    public $new_password_repeat;
    private $_user = false;
    
    public function rules()
    {
        return [
            [['password', 'new_password', 'new_password_repeat'], 'required'],
            [['password', 'new_password', 'new_password_repeat'], 'string', 'min' => 6, 'max' => 32],
            ['password', 'validatePassword'],
            ['new_password', 'compare'],
        ];
    }

    public function clearAttributes()
    {
        $this->password = '';
        $this->new_password = '';
        $this->new_password_repeat = '';
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect password.');
            }
        }
    }
    
    /**
     * Change password.
     * @return boolean Whether the password changed.
     */
    public function changePassword()
    {
        if ($this->validate()) {
            if (!($user = $this->getUser())) {
                return false;
            }
            if (!$user->applyForNewPassword()) {
                return false;
            }
            return $user->resetPassword($this->new_password, $user->getPasswordResetToken());
        }
        return false;
    }

    /**
     * Set user.
     * @param User|string|integer $user
     */
    public function setUser($user)
    {
        if ($user instanceof User) {
            $this->_user = $user;
            return true;
        }
        $this->_user = null;
        return false;
    }

    /**
     * Finds user.
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->_user;
    }
}