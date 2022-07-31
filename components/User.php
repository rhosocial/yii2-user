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

namespace rhosocial\user\components;

use rhosocial\user\models\LoginMethod\ID;
use rhosocial\user\models\LoginMethod\Username;
use rhosocial\user\rbac\roles\Admin;
use rhosocial\user\rbac\roles\Webmaster;
use yii\base\Event;

/**
 * Class User
 * @package rhosocial\user\web\components
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class User extends \rhosocial\base\models\web\User
{
    public function init()
    {
        parent::init();
        $this->on(static::EVENT_AFTER_LOGIN, [$this, 'onRecordLogin']);
    }

    /**
     * @param Event $event
     */
    public function onRecordLogin($event)
    {
        return $event->sender->identity->recordLogin();
    }

    /**
     * @return bool|mixed
     */
    public function getIsAdmin()
    {
        if ($this->getIsGuest()) {
            return false;
        }
        return $this->can((new Admin)->name, $this->identity);
    }

    /**
     * @return bool|mixed
     */
    public function getIsWebmaster()
    {
        if ($this->getIsGuest()) {
            return false;
        }
        return $this->can((new Webmaster)->name, $this->identity);
    }

    const LOGIN_BY_ID = 'id';
    const LOGIN_BY_USERNAME = 'username';

    /**
     * Get the priority of login.
     * Array value is login method class name.
     * @return array
     */
    public function getLoginPriority()
    {\Yii::$app->user;
        return [
            self::LOGIN_BY_ID => ID::class,
            self::LOGIN_BY_USERNAME => Username::class,
        ];
    }
}
