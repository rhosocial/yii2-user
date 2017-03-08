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

namespace rhosocial\user\security;

use yii\base\ModelEvent;
use yii\base\InvalidParamException;

/**
 * This trait provides password history operation for User model.
 *
 * @property-read PasswordHistory[] $passwordHistories
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
trait UserPasswordHistoryTrait
{
    /**
     * @var string|false Password History class name. If you do not need password
     * history model, please set it false.
     */
    public $passwordHistoryClass = false;
    
    /**
     * Get password history.
     * @return boolean|PasswordHistory[] False if password history invalid.
     */
    public function getPasswordHistories()
    {
        if (empty($this->passwordHistoryClass) || !class_exists($this->passwordHistoryClass)) {
            return false;
        }
        $class = $this->passwordHistoryClass;
        return $class::find()->createdBy($this)->orderByCreatedAt(SORT_DESC)->all();
    }
    
    /**
     * 
     * @param ModelEvent $event
     * @return boolean
     */
    public function onAddPasswordToHistory($event)
    {
        $password = $event->data;
        $sender = $event->sender;
        /* @var $sender static */
        if (empty($password)) {
            $password = ['pass_hash' => $sender->{$sender->passwordHashAttribute}];
        }
        if (empty($sender->passwordHistoryClass) || !class_exists($sender->passwordHistoryClass)) {
            return false;
        }
        $class = $sender->passwordHistoryClass;
        if (array_key_exists('pass_hash', $password)) {
            return $class::addHash($password['pass_hash'], $this);
        }
        if (array_key_exists('password', $password)) {
            return $class::add($password['password'], $this);
        }
        return false;
    }

    /**
     * Add password to history.
     * Note: Please specify password history model before using this method.
     *
     * @param string $password the password to be added.
     * @return boolean whether the password added. False if password history class not specified.
     * @throws InvalidParamException throw if password existed.
     */
    public function addPasswordHistory($password)
    {
        if (!empty($this->passwordHistoryClass) && class_exists($this->passwordHistoryClass)) {
            $class = $this->passwordHistoryClass;
            return $class::add($password, $this);
        }
        return false;
    }
    
    /**
     * Add password hash to history.
     * Note: Please specify password history model before using this method.
     *
     * @param string $pass_hash Password hash to be added.
     * @return boolean whether the password hash added. False if password history class not specified.
     * @throws InvalidParamException throw if password existed.
     */
    public function addPasswordHashToHistory($pass_hash)
    {
        if (!empty($this->passwordHistoryClass) && class_exists($this->passwordHistoryClass)) {
            $class = $this->passwordHistoryClass;
            return $class::addHash($pass_hash, $this);
        }
        return false;
    }
    
    public function getPasswordHashRules()
    {
        $rules = parent::getPasswordHashRules();
        $rules[] = [
            [$this->passwordHashAttribute], 'checkPasswordNotUsed', 'when' => function() {
                $class = $this->passwordHistoryClass;
                if (empty($class) || !class_exists($class)) {
                    return false;
                }
                $noInit = new $class();
                return !$noInit->allowDuplicatePassword && !$this->getIsNewRecord();
            }
        ];
        return $rules;
    }

    public function checkPasswordNotUsed($attribute, $params, $validator)
    {
        $class = $this->passwordHistoryClass;
        return !$class::isUsed($this->_password, $this);
    }
    
}