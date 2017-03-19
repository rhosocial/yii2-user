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

namespace rhosocial\user\models\log;

use rhosocial\user\User;
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
    public $loginLogClass = Login::class;
    
    /**
     * Get login logs.
     * @return Login[]
     */
    public function getLoginLogs()
    {
        /* @var $this User */
        $class = $this->loginLogClass;
        try {
            return $class::getLatests($this, 'all');
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
        try {
            return $class::getLatests($this, 1)[0];
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage(), __METHOD__);
            return null;
        }
    }
}
