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
class LoginLog extends BaseBlameableModel
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

    /**
     *
     */
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
     * @return integer
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
            return 0;
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
            return 0;
        }
        $count = 0;
        $host = $this->host;
        /* @var $host \rhosocial\user\User */
        foreach (static::find()
                ->createdBy($host)
                ->andWhere(['<=', $this->createdAtAttribute, $this->offsetDatetime($this->currentUtcDatetime(), -$limit)])
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

    const STATUS_NORMAL = 0x000;
    const STATUS_ABNORMAL_UNUSUAL_LOCATION = 0x001;
    const STATUS_ABNORMAL_TOO_MANY_WRONG_PASSWORD_ATTEMPTS = 0x002;
    const STATUS_ABNORMAL_UNUSUAL_DEVICE = 0x003;
    
    public static $statuses = [
        self::STATUS_NORMAL => 'Normal',
        self::STATUS_ABNORMAL_UNUSUAL_LOCATION => 'Abnormal (Unusual location)',
        self::STATUS_ABNORMAL_TOO_MANY_WRONG_PASSWORD_ATTEMPTS => 'Abnormal (Too many wrong password attempts)',
        self::STATUS_ABNORMAL_UNUSUAL_DEVICE => 'Abnormal (Unusual device)',
    ];

    const DEVICE_UNKNOWN = 0x000;
    const DEVICE_PC_NO_CLASSIFICATION = 0x010;
    const DEVICE_PC_WINDOWS_BROWSER = 0x011;
    const DEVICE_PC_LINUX_BROWSER = 0x012;
    const DEVICE_PC_OSX_BROWSER = 0x013;

    const DEVICE_MOBILE_NO_CLASSICATION = 0x020;
    const DEVICE_MOBILE_ANDROID_BROWSER = 0x021;
    const DEVICE_MOBILE_WINDOWSPHONE_BROWSER = 0x022;
    const DEVICE_MOBILE_IOS_BROWSER = 0x023;

    const DEVICE_PC_WINDOWS_APPLICATION = 0x031;
    const DEVICE_PC_LINUX_APPLICATION = 0x032;
    const DEVICE_PC_OSX_APPLICATION = 0x033;

    const DEVICE_MOBILE_ANDROID_APPLICATION = 0x041;
    const DEVICE_MOBILE_WINDOWSPHONE_APPLICATION = 0x042;
    const DEVICE_MOBILE_IOS_APPLICATION = 0x043;

    const DEVICE_3PA_NO_CLASSIFICATION = 0x050;
    const DEVICE_3PA_PC = 0x051;
    const DEVICE_3PA_MOBILE = 0x052;
    const DEVICE_3PA_BROWSER = 0x053;
    
    public static $devices = [
        self::DEVICE_UNKNOWN => 'Unknown',
        self::DEVICE_PC_NO_CLASSIFICATION => 'PC (No classification)',
        self::DEVICE_PC_WINDOWS_BROWSER => 'PC (Windows, Browser)',
        self::DEVICE_PC_LINUX_BROWSER => 'PC (Linux, Browser)',
        self::DEVICE_PC_OSX_BROWSER => 'PC (OS X, Browser)',
        self::DEVICE_MOBILE_NO_CLASSICATION => 'Mobile (No classification)',
        self::DEVICE_MOBILE_ANDROID_BROWSER => 'Mobile (Android, Browser)',
        self::DEVICE_MOBILE_WINDOWSPHONE_BROWSER => 'Mobile (Windows Phone, Browser)',
        self::DEVICE_MOBILE_IOS_BROWSER => 'Mobile (iOS, Browser)',
        self::DEVICE_PC_WINDOWS_BROWSER => 'PC (Windows, Application)',
        self::DEVICE_PC_LINUX_APPLICATION => 'PC (Linux, Application)',
        self::DEVICE_PC_OSX_APPLICATION => 'PC (OS X, Application)',
        self::DEVICE_MOBILE_ANDROID_APPLICATION => 'Mobile (Android, Application)',
        self::DEVICE_MOBILE_WINDOWSPHONE_APPLICATION => 'Mobile (Windows Phone, Application)',
        self::DEVICE_MOBILE_IOS_APPLICATION => 'Mobile (iOS, Application)',
        self::DEVICE_3PA_NO_CLASSIFICATION => 'Third party authorization (No classification)',
        self::DEVICE_3PA_PC => 'Third party authorization (PC)',
        self::DEVICE_3PA_MOBILE => 'Third party authorization (Mobile)',
        self::DEVICE_3PA_BROWSER => 'Third party authorization (Browser)',
    ];

    /**
     * @return array
     */
    public function getLoginRules()
    {
        return [
            ['status', 'in', 'range' => array_keys(static::$statuses)],
            ['status', 'default', 'value' => self::STATUS_NORMAL],
            ['device', 'in', 'range' => array_keys(static::$devices)],
            ['device', 'default', 'value' => self::DEVICE_UNKNOWN],
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge($this->getLoginRules(), parent::rules());
    }

    /**
     * @return mixed|null
     */
    public function getStatusDesc()
    {
        if (array_key_exists($this->status, static::$statuses)) {
            return static::$statuses[$this->status];
        }
        return null;
    }

    /**
     * @return mixed|null
     */
    public function getDeviceDesc()
    {
        if (array_key_exists($this->device, static::$devices)) {
            return static::$devices[$this->device];
        }
        return null;
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%log_login}}';
    }

    const GET_ALL_LATESTS = 'all';
    
    /**
     * Get latest ones.
     * @param User $user
     * @param integer $limit
     * @return static[]
     */
    public static function getLatests(User $user, $limit = 1)
    {
        $query = static::find()->createdBy($user)->orderByCreatedAt(SORT_DESC);
        if ($limit == self::GET_ALL_LATESTS || !is_int($limit) || $limit < 1) {
            return $query->all();
        }
        return $query->limit($limit)->all();
    }
}
