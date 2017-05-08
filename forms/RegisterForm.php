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
use rhosocial\user\Profile;
use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class RegisterForm extends Model
{
    public $nickname;
    public $username = false;
    public $password;
    public $password_repeat;
    public $first_name;
    public $last_name;
    public $gender = 1;
    public $userClass;
    public $model = null;
    public $continue = 0;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Username'),
            'nickname' => Yii::t('user', 'Nickname'),
            'password' => Yii::t('user', 'Password'),
            'password_repeat' => Yii::t('user', 'Password Repeat'),
            'first_name' => Yii::t('user', 'First Name'),
            'last_name' => Yii::t('user', 'Last Name'),
            'gender' => Yii::t('user', 'Gender'),
            'continue' => Yii::t('user', 'Continue to register'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'continue' => Yii::t('user', 'If you want to register new users consecutively, check this option.'),
        ];
    }

    /**
     * @return User
     */
    protected function getNoInitUser()
    {
        $userClass = $this->userClass;
        return $userClass::buildNoInitModel();
    }

    /**
     * @return Username
     */
    protected function getNoInitUsername()
    {
        $usernameClass = $this->getNoInitUser()->usernameClass;
        return $usernameClass::buildNoInitModel();
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->userClass)) {
            $this->userClass = Yii::$app->user->identityClass;
        }
        $noInit = $this->getNoInitUser();
        /* @var $noInit User */
        if (class_exists($noInit->usernameClass)) {
            $this->username = '';
        } else {
            $this->username = false;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['nickname', 'password', 'password_repeat', 'first_name', 'last_name', 'gender'], 'required'],
            [['nickname'], 'string', 'max' => 32],
            [['password', 'password_repeat'], 'string', 'min' => 6, 'max' => 32],
            ['password', 'compare'],
            [['first_name', 'last_name'], 'string', 'max' => 255],
            ['gender', 'in', 'range' => array_keys(Profile::$genders)],
            ['continue', 'integer'],
        ];
        if (is_string($this->username)) {
            $rules = array_merge($rules, [
                ['username', 'required'],
                ['username', 'string', 'max' => 32],
                ['username', 'unique', 'targetClass' => $this->getNoInitUser()->usernameClass, 'targetAttribute' => $this->getNoInitUsername()->contentAttribute, 'message' => Yii::t('user', 'The username has been used.')]
            ]);
        }
        return $rules;
    }

    /**
     * Register user with current model.
     * @return bool
     */
    public function register()
    {
        if ($this->validate()) {
            $class = $this->userClass;
            $user = new $class(['password' => $this->password]);
            /* @var $user User */
            $profile = $user->createProfile(['nickname' => $this->nickname, 'first_name' => $this->first_name, 'last_name' => $this->last_name, 'gender' => $this->gender]);
            $models[] = $profile;
            if (is_string($this->username)) {
                $username = $user->createUsername($this->username);
                $models[] = $username;
            }
            $result = $user->register($models);
            if ($result == true) {
                $this->model = $user;
            }
            return $result;
        }
        return false;
    }
}
