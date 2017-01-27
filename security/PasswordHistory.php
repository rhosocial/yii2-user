<?php

/**
 *   _   __ __ _____ _____ ___  ____  _____
 *  | | / // // ___//_  _//   ||  __||_   _|
 *  | |/ // /(__  )  / / / /| || |     | |
 *  |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2017 vistart
 * @license https://vistart.me/license/
 */
namespace rhosocial\user\security;

use rhosocial\user\User;
use rhosocial\base\models\models\BaseBlameableModel;
use Yii;
use yii\base\InvalidParamException;

/**
 * This model holds password that have been used.
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class PasswordHistory extends BaseBlameableModel
{
    public $idAttribute = false;
    public $updatedAtAttribute = false;
    public $updatedByAttribute = false;
    public $passwordHashAttribute = 'pass_hash';
    
    public static function tableName()
    {
        return "{{%password_history}}";
    }
    
    public function validate($password)
    {
        return Yii::$app->security->validatePassword($password, $this->{$this->passwordHashAttribute});
    }
    
    /**
     * Check whether the password has been used.
     * @param string $password
     * @param User $user
     */
    public static function isUsed($password, $user = null)
    {
        if (!User::isValid($user)) {
            throw new InvalidParamException('User Invalid.');
        }
        $passwords = static::find()->createdBy($user)->all();
        foreach ($passwords as $p) {
            /* @var $p static */
            if ($p->validate($password)) {
                return $p;
            }
        }
        return false;
    }
    
    public function setPassword($password)
    {
        $this->{$this->passwordHashAttribute} = Yii::$app->security->generatePasswordHash($password);
    }
    
    /**
     * 
     * @param string $password
     * @param User $user
     */
    public static function add($password, $user = null)
    {
        if (static::isUsed($password, $user)) {
            throw new InvalidParamException('Password exists.');
        }
        $p = $user->create(static::class, ['password' => $password]);
        /* @var $p static */
        return $p->save();
    }
    
    /**
     * 
     * @param User $user
     * @return static
     * @throws InvalidParamException
     */
    public static function first($user = null)
    {
        if (!User::isValid($user)) {
            throw new InvalidParamException('User Invalid.');
        }
        return static::find()->createdBy($user)->orderByCreatedAt()->one();
    }
    
    /**
     * 
     * @param User $user
     * @return static
     * @throws InvalidParamException
     */
    public static function last($user = null)
    {
        if (!User::isValid($user)) {
            throw new InvalidParamException('User Invalid.');
        }
        return static::find()->createdBy($user)->orderByCreatedAt('DESC')->one();
    }
}