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
     * Get user within $attribute.
     * @param mixed $attribute The attribute which is used to identify user.
     * For example, the attribute value of the "User ID" is an eight-digit number.
     * @return User|null The user instance if found, or null if not.
     */
    public static function getUser($attribute);

    /**
     * Validate that the attribute meets the rules.
     * @param mixed $attribute The attribute which is used to identify user.
     * @return bool Validation pass or not.
     */
    public static function validate($attribute);
}
