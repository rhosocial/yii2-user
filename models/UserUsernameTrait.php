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

namespace rhosocial\user\models;

use rhosocial\base\models\queries\BaseBlameableQuery;

/**
 * Trait UserUsernameTrait
 *
 * @property string|Username $username
 * @package rhosocial\user\models
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
trait UserUsernameTrait
{
    public $usernameClass = false;

    /**
     * Check whether this user enables the username feature or not.
     * @return boolean
     */
    public function hasUsername()
    {
        if ($this->usernameClass === false || !is_string($this->usernameClass) || !class_exists($this->usernameClass)) {
            return false;
        }
        return true;
    }

    /**
     * Get username.
     * @return BaseBlameableQuery
     */
    public function getUsername()
    {
        if (!$this->hasUsername()) {
            return null;
        }
        $usernameClass = $this->usernameClass;
        $noInit = $usernameClass::buildNoInitModel();
        /* @var $noInit Username */
        return $this->hasOne($usernameClass, [$noInit->createdByAttribute => $this->guidAttribute]);
    }

    /**
     * Create or get username.
     * @param $username
     * @return null|Username
     */
    public function createUsername($username)
    {
        $usernameClass = $this->usernameClass;
        if (!is_string($usernameClass) || empty($usernameClass)) {
            return null;
        }
        $model = $usernameClass::findOne($this->getGUID());
        if (!$model) {
            $model = $this->create($usernameClass);
            $model->setGUID($this->getGUID());
        }
        $model->content = $username;
        return $model;
    }

    /**
     * Set username.
     * @param string|Username $username
     * @return bool
     */
    public function setUsername($username = null)
    {
        if ($username === null && ($model = $this->getUsername()->one())) {
            return $model->delete() > 0;
        }
        if ($username instanceof Username) {
            $username = $username->content;
        }
        $model = $this->createUsername($username);
        return $model->save();
    }

    /**
     * Remove username.
     */
    public function removeUsername()
    {
        return $this->setUsername(null);
    }
}
