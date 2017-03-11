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

namespace rhosocial\user\rbac;

use rhosocial\user\User;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Assignment extends \yii\rbac\Assignment
{
    /**
     * @var string|User user ID (see [[\rhosocial\user\User::guid]])
     */
    public $userGuid;
}