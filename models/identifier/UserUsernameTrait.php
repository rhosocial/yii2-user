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

namespace rhosocial\user\models\identifier;

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
    public function hasEnabledUsername()
    {
        if ($this->usernameClass === false || !is_string($this->usernameClass) || !class_exists($this->usernameClass)) {
            return false;
        }
        return true;
    }

    /**
     * Get username.
     * This method may return null, please consider processing the abnormal conditions.
     * @return BaseBlameableQuery
     */
    public function getUsername()
    {
        if (!$this->hasEnabledUsername()) {
            return null;
        }
        $usernameClass = $this->usernameClass;
        $noInit = $usernameClass::buildNoInitModel();
        /* @var $noInit Username */
        return $this->hasOne($usernameClass, [$noInit->createdByAttribute => $this->guidAttribute]);
    }

    /**
     * Create or get username.
     * @param string $username
     * @return null|Username
     */
    public function createUsername(string|null $username = null)
    {
        if (!$this->hasEnabledUsername()) {
            return null;
        }
        $usernameClass = $this->usernameClass;
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
     * @return bool
     */
    public function removeUsername()
    {
        return $this->setUsername(null);
    }
}
