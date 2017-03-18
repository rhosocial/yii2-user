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

use rhosocial\base\models\models\BaseBlameableModel;
use rhosocial\user\User;
use Yii;
use yii\base\ModelEvent;

/**
 * Login log.
 *
 * If you're using MySQL, we recommend that you create a data table using the following statement:
 ```SQL
CREATE TABLE `log_login` (
  `guid` varbinary(16) NOT NULL,
  `id` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `user_guid` varbinary(16) NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `ip_type` smallint(6) NOT NULL DEFAULT '4',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `status` int(11) NOT NULL DEFAULT '0',
  `device` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`guid`),
  UNIQUE KEY `login_log_id_unique` (`guid`,`id`),
  KEY `login_log_creator_fk` (`user_guid`),
  CONSTRAINT `login_log_creator_fk` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
 ```
 *
 * @property integer $status Login status.
 * @property integer $device Login device. 
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Login extends BaseBlameableModel
{
    public $contentAttribute = false;
    public $updatedAtAttribute = false;
    public $updatedByAttribute = false;
    
    const LIMIT_NO_LIMIT = 0x0;
    const LIMIT_MAX = 0x1;
    const LIMIT_DURATION = 0x2;
    
    public $limitType = 0x3;
    public $limitMax = 100;
    public $limitDuration = 90 * 86400;
    
    public function init()
    {
        if (($this->limitType & static::LIMIT_MAX) && ($this->limitMax < 2 || !is_int($this->limitMax))) { // at least 2 records.
            $this->limitMax = 100;  // 100 records.
        }
        if (($this->limitType & static::LIMIT_DURATION) && ($this->limitDuration < 86400 || !is_int($this->limitDuration))) { // at least one day.
            $this->limitDuration = 90 * 86400; // 90 Days.
        }
        if ($this->limitType > 0) {
            $this->on(static::EVENT_AFTER_INSERT, [$this, 'onDeleteExtraRecords']);
        }
        parent::init();
    }
    
    /**
     * 
     * @param ModelEvent $event
     */
    public function onDeleteExtraRecords($event)
    {
        $sender = $event->sender;
        /* @var $sender static */
        Yii::info('Login Log limit type:' . $sender->limitType, __METHOD__);
        $result = 0;
        if ($sender->limitType & static::LIMIT_MAX) {
            $result += $sender->deleteExtraRecords();
        }
        if ($sender->limitType & static::LIMIT_DURATION) {
            $result += $sender->deleteExpiredRecords();
        }
        return $result;
    }
    
    /**
     * Delete extra records.
     * @return integer The total of rows deleted.
     */
    protected function deleteExtraRecords()
    {
        try {
            $limit = (int)($this->limitMax);
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage(), __METHOD__);
        }
        $host = $this->host;
         /* @var $host \rhosocial\user\User */
        $count = static::find()->createdBy($host)->count();
        Yii::info($host->getReadableGUID() . " has $count login logs.", __METHOD__);
        if ($count > $limit) {
            foreach (static::find()->createdBy($host)->orderByCreatedAt()->limit($count - $limit)->all() as $login) {
                /* @var $login static */
                $result = $login->delete();
                if (YII_ENV_DEV) {
                    Yii::info($host->getReadableGUID() . ": ($result) login record created at (" . $login->getCreatedAt() . ") was just deleted.", __METHOD__);
                }
            }
        }
        return $count - $limit;
    }
    
    /**
     * Delete expired records.
     * @return integer The total of rows deleted.
     */
    protected function deleteExpiredRecords()
    {
        try {
            $limit = (int)($this->limitDuration);
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage(), __METHOD__);
        }
        $count = 0;
        $host = $this->host;
        /* @var $host \rhosocial\user\User */
        foreach (static::find()
                ->createdBy($host)
                ->andWhere(['<=', $this->createdAtAttribute, $this->offsetDatetime(null, -$limit)])
                ->all() as $login) {
            /* @var $login static */
            $result = $login->delete();
            $count += $result;
            if (YII_ENV_DEV) {
                Yii::info($host->getReadableGUID() . ": ($result) login record created at (" . $login->getCreatedAt() . ") was just deleted.", __METHOD__);
            }
        }
        return $count;
    }
    
    public static $statuses = [
        0x000 => 'Normal',
        0x001 => 'Abnormal (Unusual location)',
        0x002 => 'Abnormal (Too many wrong password attempts)',
        0x003 => 'Abnormal (Unusual device)',
    ];
    
    public static $devices = [
        0x000 => 'Unknown',
        0x010 => 'PC (No classification)',
        0x011 => 'PC (Windows, Browser)',
        0x012 => 'PC (Linux, Browser)',
        0x013 => 'PC (OS X, Browser)',
        0x020 => 'Mobile (No classification)',
        0x021 => 'Mobile (Android, Browser)',
        0x022 => 'Mobile (Windows Phone, Browser)',
        0x023 => 'Mobile (iOS, Browser)',
        0x031 => 'PC (Windows, Application)',
        0x032 => 'PC (Linux, Application)',
        0x033 => 'PC (OS X, Application)',
        0x041 => 'Mobile (Android, Application)',
        0x042 => 'Mobile (Windows Phone, Application)',
        0x043 => 'Mobile (iOS, Application)',
        0x050 => 'Third party authorization (No classification)',
        0x051 => 'Third party authorization (PC)',
        0x052 => 'Third party authorization (Mobile)',
        0x053 => 'Third party authorization (Browser)',
    ];
    
    public function getLoginRules()
    {
        return [
            ['status', 'in', 'range' => array_keys(static::$statuses)],
            ['device', 'in', 'range' => array_keys(static::$devices)],
        ];
    }
    
    public function rules()
    {
        return array_merge($this->getLoginRules(), parent::rules());
    }
    
    public function getStatus()
    {
        if (array_key_exists($this->status, static::$statuses)) {
            return static::$statuses[$this->status];
        }
        return null;
    }
    
    public function getDevice()
    {
        if (array_key_exists($this->device, static::$devices)) {
            return static::$devices[$this->device];
        }
        return null;
    }
    
    public static function tableName()
    {
        return '{{%log_login}}';
    }
    
    /**
     * Get latest ones.
     * @param User $user
     * @param integer $limit
     * @return static[]
     */
    public static function getLatests(User $user, $limit = 1)
    {
        $query = static::find()->createdBy($user)->orderByCreatedAt(SORT_DESC);
        if ($limit == 'all' || !is_int($limit) || $limit < 1) {
            return $query->all();
        }
        return $query->limit($limit)->all();
    }
}
