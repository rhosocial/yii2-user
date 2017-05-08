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
use Yii;

/**
 * Class ID
 * @package rhosocial\user\models\LoginMethod
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class ID implements MethodInterface
{
    /**
     * @param mixed $attribute
     * @return User|null
     */
    public static function getUser($attribute)
    {
        if (!static::validate($attribute)) {
            return null;
        }
        $userClass = Yii::$app->user->identityClass;
        return $userClass::find()->id($attribute)->one();
    }

    /**
     * Validate whether the attribute is valid.
     * @param mixed $attribute
     * @return bool
     */
    public static function validate($attribute)
    {
        $userClass = Yii::$app->user->identityClass;
        $regex = $userClass::$idRegex;
        $result = preg_match($regex, $attribute);
        return is_int($result) && $result > 0;
    }
}
