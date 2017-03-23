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

use rhosocial\helpers\Number;
use rhosocial\user\User;
use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $password;
    public $new_password;
    public $new_password_repeat;
    public $userClass;
    private $_user = false;
    
    public function init()
    {
        if (empty($this->userClass)) {
            $this->userClass = Yii::$app->user->identityClass;
        }
    }
    
    public function rules()
    {
        return [
            [['password', 'new_password', 'new_password_repeat'], 'required'],
            [['password', 'new_password', 'new_password_repeat'], 'string', 'min' => 6, 'max' => 32],
            ['password', 'validatePassword'],
            ['new_password', 'compare'],
        ];
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
                $this->addError($attribute, 'Incorrect username or password.');
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
        $class = $this->userClass;
        if ($user instanceof User) {
            $this->_user = $class::find()->guid($user->getGUID())->one();
            return true;
        }
        if (is_string($user) && preg_mapreg_match(Number::GUID_REGEX, $user)) {
            $this->_user = $class::find()->guid($user)->one();
            return true;
        }
        if (is_numeric($user) || is_int($user)) {
            $this->_user = $class::find()->id($user)->one();
            return true;
        }
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