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
class LoginForm extends Model
{
    public $id;
    public $password;
    public $rememberMe = true;
    public $userClass;
    private $_user = false;

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'password' => Yii::t('user', 'Password'),
            'rememberMe' => Yii::t('user', 'Remember Me'),
        ];
    }

    /**
     *
     */
    public function init()
    {
        if (empty($this->userClass)) {
            $this->userClass = Yii::$app->user->identityClass;
        }
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'password'], 'required'],
            ['id', 'validateId'],
            ['password', 'validatePassword'],
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Validates the ID.
     *
     * @param $attribute
     * @param $params
     */
    public function validateId($attribute, $params)
    {
        $loginPriority = Yii::$app->user->getLoginPriority();
        foreach ($loginPriority as $item) {
            if (preg_match($item, $this->$attribute)) {
                return;
            }
        }
        $this->addError($attribute, Yii::t('user', 'Incorrect ID.'));
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
                $this->addError($attribute, Yii::t('user', 'Incorrect ID or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided id and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[id]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $class = $this->userClass;
            $this->_user = $class::find()->id($this->id)->one();
        }

        return $this->_user;
    }
}
