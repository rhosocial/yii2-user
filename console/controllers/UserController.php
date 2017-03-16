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

namespace rhosocial\user\console\controllers;

use rhosocial\user\User;
use rhosocial\user\Profile;
use yii\console\Controller;
use yii\console\Exception;
use Yii;

/**
 * The simple operations associated with User.
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UserController extends Controller
{
    public $userClass;
    
    public $defaultAction = 'show';
    
    protected function checkUserClass()
    {
        $userClass = $this->userClass;
        if (!class_exists($userClass)) {
            throw new Exception('User Class Invalid.');
        }
        if (!((new $userClass()) instanceof User)) {
            throw new Exception('User Class(' . $userClass . ') does not inherited from `\rhosocial\user\User`.');
        }
        return $userClass;
    }
    
    /**
     * Get user from database.
     * @param User|string|integer $user
     * @return User
     */
    protected function getUser($user)
    {
        $userClass = $this->checkUserClass();
        if (is_numeric($user)) {
            $user = $userClass::find()->id($user)->one();
        } elseif (is_string($user) && strlen($user)) {
            $user = $userClass::find()->guid($user)->one();
        }
        if (!$user || $user->getIsNewRecord()) {
            throw new Exception('User Not Registered.');
        }
        return $user;
    }
    
    /**
     * Register new User.
     * @param string $password Password.
     * @param string $nickname If profile contains this property, this parameter is required.
     * @param string $first_name If profile contains this property, this parameter is required.
     * @param string $last_name If profile contains this propery, this parameter is required.
     */
    public function actionRegister($password, $nickname = null, $first_name = null, $last_name = null)
    {
        $userClass = $this->checkUserClass();
        
        $user = new $userClass(['password' => $password]);
        /* @var $user User */
        $profile = $user->createProfile(['nickname' => $nickname, 'first_name' => $first_name, 'last_name' => $last_name]);
        /* @var $profile Profile */
        try {
            is_null($profile) ? $user->register(): $user->register([$profile]);
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        echo "User Registered:\n";
        return $this->actionShow($user);
    }
    
    /**
     * Deregister user.
     * @param User|string|integer $user The user to be deregistered.
     * @return boolean
     */
    public function actionDeregister($user)
    {
        $user = $this->getUser($user);
        if ($user->deregister()) {
            echo "User (" . $user->getID() . ") Deregistered.\n";
            return true;
        }
        return false;
    }
    
    /**
     * Show User Information.
     * @param User|string|integer $user
     */
    public function actionShow($user, $guid = false, $pass_hash = false, $access_token = false, $auth_key = false)
    {
        $user = $this->getUser($user);
        echo Yii::t('app', 'User') . " (" . $user->getID() . "), " . Yii::t('app', 'registered at') . " (" . $user->getCreatedAt() . ")"
                . ($user->getCreatedAt() == $user->getUpdatedAt() ? "" : ", " . Yii::t('app', 'last updated at') . " (" . $user->getUpdatedAt() . ")") .".\n";
        return true;
    }
    
    /**
     * Show statistics.
     * @param User|string|integer $user
     * @return boolean
     */
    public function actionStat($user = null)
    {
        if ($user === null) {
            $count = User::find()->count();
            echo "Total number of user(s): " . $count . "\n";
            if ($count == 0) {
                return true;
            }
            $last = User::find()->orderByCreatedAt(SORT_DESC)->one();
            /* @var $last User */
            echo "Latest user (" . $last->getID() . ") registered at " . $last->getCreatedAt() . "\n";
            return true;
        }
        $user = $this->getUser($user);
        return true;
    }
}