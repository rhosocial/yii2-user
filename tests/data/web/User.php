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

namespace rhosocial\user\tests\data\web;

use rhosocial\user\models\log\Login;
use Yii;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class User extends \rhosocial\base\models\web\User
{
    public function init()
    {
        parent::init();
        $this->on(static::EVENT_AFTER_LOGIN, [$this, 'onRecordLogon']);
    }
    
    /**
     * 
     * @param \yii\db\Event $event
     */
    public function onRecordLogon($event)
    {
        $user = $event->sender->identity;
        /* @var $user \rhosocial\user\tests\data\User */
        $log = $user->create(Login::class, ['device' => 0x011]); // PC (Windows, Browser)
        try {
            return $log->save();
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }
}