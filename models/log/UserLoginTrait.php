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

namespace rhosocial\user\models\log;

use rhosocial\user\models\User;
use Yii;

/**
 * This trait provides login log access methods.
 *
 * @property-read Login[] $loginLogs
 * @property-read Login $latestLoginLog
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
trait UserLoginTrait
{
    public $loginLogClass = false;
    
    /**
     * Get login logs.
     * @return Login[]
     */
    public function getLoginLogs()
    {
        /* @var $this User */
        $class = $this->loginLogClass;
        if (empty($class)) {
            return [];
        }
        try {
            return $class::getLatests($this, Login::GET_ALL_LATESTS);
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage(), __METHOD__);
            return [];
        }
    }
    
    /**
     * Get latest login log.
     * @return Login
     */
    public function getLatestLoginLog()
    {
        /* @var $this User */
        $class = $this->loginLogClass;
        if (empty($class)) {
            return [];
        }
        try {
            return $class::getLatests($this, 1)[0];
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage(), __METHOD__);
            return null;
        }
    }

    /**
     * Record login.
     * @param array $config
     * @return mixed
     */
    public function recordLogin($config = [])
    {
        if (empty($this->loginLogClass)) {
            Yii::warning("`$loginLogClass` not defined. Login logs are not recorded.", __METHOD__);
            return false;
        }
        $log = $this->create($this->loginLogClass, $config);
        try {
            return $log->save();
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage(), __METHOD__);
        }
    }
}
