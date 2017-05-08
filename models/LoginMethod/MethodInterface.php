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

namespace rhosocial\user\models\LoginMethod;

use rhosocial\user\User;

/**
 * Interface MethodInterface
 * @package rhosocial\user\models\LoginMethod
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
interface MethodInterface
{
    /**
     * @param mixed $attribute
     * @return User|null
     */
    public static function getUser($attribute);

    /**
     * @param mixed $attribute
     * @return bool
     */
    public static function validate($attribute);
}
