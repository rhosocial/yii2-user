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
 * Class Username
 * @package rhosocial\user\models\LoginMethod
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Username implements MethodInterface
{
    /**
     * @param mixed $attribute
     * @return User|null
     */
    public static function getUser($attribute)
    {
        if (!static::validate($attribute)) {
            return false;
        }
        $userClass = Yii::$app->user->identityClass;
        $noInit = $userClass::buildNoInitModel();
        /* @var $noInit User */
        if (class_exists($noInit->usernameClass)) {
            $class = $noInit->usernameClass;
            try {
                return $class::find()->content($attribute)->one()->host;
            } catch (\Exception $ex) {
                return null;
            }
        }
        return null;
    }

    /**
     * @param mixed $attribute
     * @return bool
     */
    public static function validate($attribute)
    {
        $userClass = Yii::$app->user->identityClass;
        $noInit = $userClass::buildNoInitModel();
        /* @var $noInit User */
        if (class_exists($noInit->usernameClass)) {
            $class = $noInit->usernameClass;
            $result = preg_match($class::$regex, $attribute);
            return is_int($result) && $result > 0;
        }
        return false;
    }
}
