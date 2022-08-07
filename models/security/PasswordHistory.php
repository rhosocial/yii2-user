<?php

/**
 *   _   __ __ _____ _____ ___  ____  _____
 *  | | / // // ___//_  _//   ||  __||_   _|
 *  | |/ // /(__  )  / / / /| || |     | |
 *  |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2022 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\models\security;

use rhosocial\user\models\User;
use rhosocial\base\models\models\BaseBlameableModel;
use Yii;
use yii\base\InvalidParamException;

/**
 * This model holds passwords that have been used.
 *
 * @property-write string $password
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class PasswordHistory extends BaseBlameableModel
{
    public $idAttribute = false;
    public $updatedAtAttribute = false;
    public $updatedByAttribute = false;
    public $enableIP = false;
    public $contentAttribute = false;
    public $passwordHashAttribute = 'pass_hash';
    
    public static function tableName()
    {
        return '{{%password_history}}';
    }
    
    /**
     * Validate password.
     *
     * @param string $password Password or Password Hash.
     * @return boolean
     */
    public function validatePassword($password)
    {
        if (static::judgePasswordHash($password)) {
            return $this->{$this->passwordHashAttribute} == $password;
        }
        return Yii::$app->security->validatePassword($password, $this->{$this->passwordHashAttribute});
    }
    
    /**
     * Check whether the password has been used.
     * @param string $password Password or Password Hash.
     * @param User $user
     * @return false|static The first validated password model, or false if not validated.
     */
    public static function isUsed($password, $user = null)
    {
        if (!User::isValid($user)) {
            throw new InvalidParamException('User Invalid.');
        }
        $passwords = static::find()->createdBy($user)->all();
        foreach ($passwords as $p) {
            /* @var $p static */
            if ($p->validatePassword($password)) {
                return $p;
            }
        }
        return false;
    }
    
    /**
     * Set password.
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->{$this->passwordHashAttribute} = Yii::$app->security->generatePasswordHash($password);
    }
    
    protected static function judgePasswordHash($password)
    {
        return strpos($password, '$2y$') !== false;
    }
    
    /**
     * Add password to history.
     *
     * @param string $password Password or Password Hash.
     * @param User $user
     * @return boolean
     * @throws InvalidParamException throw if password existed.
     */
    public static function add($password, $user = null)
    {
        if (static::isUsed($password, $user) && !$user->allowUsedPassword) {
            throw new InvalidParamException('Password existed.');
        }
        if (static::judgePasswordHash($password)) {
            $passwordHistory = $user->create(static::class);
            $passwordHistory->{$passwordHistory->passwordHashAttribute} = $password;
        } else {
            $passwordHistory = $user->create(static::class, ['password' => $password]);
        }
        /* @var $passwordHistory static */
        return $passwordHistory->save();
    }
    
    public static function passHashIsUsed($passHash, $user = null)
    {
        if (!User::isValid($user)) {
            throw new InvalidParamException('User Invalid.');
        }
        $passwords = static::find()->createdBy($user)->all();
        foreach ($passwords as $passwordHistory) {
            /* @var $passwordHistory static */
            if ($passwordHistory->{$passwordHistory->passwordHashAttribute} == $passHash) {
                return $passwordHistory;
            }
        }
        return false;
    }
    
    public static function addHash($passHash, $user = null)
    {
        if (static::passHashIsUsed($passHash, $user)) {
            throw new InvalidParamException('Password existed.');
        }
        $noInit = static::buildNoInitModel();
        $passwordHistory = $user->create(static::class, [$noInit->passwordHashAttribute => $passHash]);
        return $passwordHistory->save();
    }
    
    /**
     * Get first password hash.
     *
     * @param User $user
     * @return static
     * @throws InvalidParamException throw if user invalid.
     */
    public static function first($user = null)
    {
        if (!User::isValid($user)) {
            throw new InvalidParamException('User Invalid.');
        }
        return static::find()->createdBy($user)->orderByCreatedAt()->one();
    }
    
    /**
     * Get last password hash.
     *
     * @param User $user
     * @return static
     * @throws InvalidParamException throw if user invalid.
     */
    public static function last($user = null)
    {
        if (!User::isValid($user)) {
            throw new InvalidParamException('User Invalid.');
        }
        return static::find()->createdBy($user)->orderByCreatedAt(SORT_DESC)->one();
    }
}
