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

namespace rhosocial\user\tests\data\models\log;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class LoginDeleteExpired extends \rhosocial\user\models\log\Login
{    
    public function init()
    {
        parent::init();
        $this->limitMax = 20;
        $this->limitDuration = 2;
        $this->limitType = static::LIMIT_DURATION;
    }
}