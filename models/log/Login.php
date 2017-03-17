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
}