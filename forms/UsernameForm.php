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

namespace rhosocial\user\forms;

use rhosocial\user\models\User;
use Yii;
use yii\base\Model;

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
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->user) {
            $this->user = Yii::$app->user->identity;
        }
        $username = $this->user->getUsername()->one();
        if (!$username) {
            $this->username = '';
        } else {
            $this->username = (string)$username;
        }
        parent::init();
    }

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
            ['username', 'unique', 'targetClass' => $this->user->usernameClass, 'targetAttribute' => $this->getNoInitUsername()->contentAttribute, 'when' => function ($model, $attribute) {
                /* @var $model static */
                $username = $model->user->getUsername()->one();
                if (!$username) {
                    return true;
                }
                return $model->$attribute != (string)$username;
            }],
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

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Username'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'username' => Yii::t('user', 'Specify a username to provide convenience for login.'),
        ];
    }
}
