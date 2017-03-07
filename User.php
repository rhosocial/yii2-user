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

namespace rhosocial\user;

use rhosocial\base\models\models\BaseUserModel;
use rhosocial\base\models\queries\BaseBlameableQuery;
use Yii;

/**
 * Common User Model.
 * This model should be stored in a relational database. You can create a foreign
 * key constraint on other models and this model.
 * 
 * If you're using MySQL, we recommend that you create a data table using the following statement:
 * 
 * ```
 * CREATE TABLE `user` (
 *   `guid` varbinary(16) NOT NULL COMMENT 'GUID',
 *   `id` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ID',
 *   `pass_hash` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Password Hash',
 *   `ip` varbinary(16) NOT NULL DEFAULT '0' COMMENT 'IP',
 *   `ip_type` tinyint(3) unsigned NOT NULL DEFAULT '4' COMMENT 'IP Address Type',
 *   `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Create Time',
 *   `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Update Time',
 *   `auth_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Authentication Key',
 *   `access_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Access Token',
 *   `password_reset_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Password Reset Token',
 *   `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Status',
 *   `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Type',
 *   `source` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Source',
 *   PRIMARY KEY (`guid`),
 *   UNIQUE KEY `user_id_unique` (`id`),
 *   KEY `user_auth_key_normal` (`auth_key`),
 *   KEY `user_access_token_normal` (`access_token`),
 *   KEY `user_password_reset_token` (`password_reset_token`),
 *   KEY `user_create_time_normal` (`created_at`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User';
 * ```
 * 
 * The fields of User table in database are following:
 * @property string $guid User's GUID. This property is used to uniquely identify a user.
 * This property is automatically generated when the class is created, we do not
 * recommend that you modify this property, unless you know the consequences of doing so.
 * This property is also regareded as the foreign key target of other models associated
 * with this model. If you have to modify this property, the foreign key constraints
 * should be updating and deleting on cascade.
 * @property string $id User Identifier No. It is a 8-digit number beginning with 4 by default.
 * @property string $pass_hash Password Hash.
 * We strongly recommend you NOT to change this property directly!
 * If you want to set or reset password, please use setPassword() magic property instead.
 * @property integer $ip IP address.
 * @property integer $ipType
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_reset_token
 * @property integer $status
 * @property integer $type
 * @property string $source
 * 
 * @property-read Profile $profile Profile. This magic property is read-only.
 * If you want to modify anyone property of Profile model, please get it first,
 * then change and save it, like following:
 * ```php
 * $profile = $user->profile;
 * $profile->nickname = 'vistart';
 * $profile->save();
 * ```
 * If $profileClass is `false`, `null` returned.
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class User extends BaseUserModel
{

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'guid' => Yii::t('app', 'GUID'),
            'id' => Yii::t('app', 'ID'),
            'pass_hash' => Yii::t('app', 'Password Hash'),
            'ip' => Yii::t('app', 'IP'),
            'ip_type' => Yii::t('app', 'IP Address Type'),
            'created_at' => Yii::t('app', 'Create Time'),
            'updated_at' => Yii::t('app', 'Update Time'),
            'auth_key' => Yii::t('app', 'Authentication Key'),
            'access_token' => Yii::t('app', 'Access Token'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'status' => Yii::t('app', 'Status'),
            'type' => Yii::t('app', 'Type'),
            'source' => Yii::t('app', 'Source'),
        ];
    }

    /**
     * @inheritdoc
     */
    public $idAttributeType = 1;

    /**
     * @inheritdoc
     */
    public $idAttributeLength = 8;

    /**
     * @inheritdoc
     */
    public $idAttributePrefix = '4';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @var string|false Profile class name. If you do not need profile model,
     * please set it false.
     */
    public $profileClass = false;
    
    /**
     * @var string|false Password History class name. If you do not need password
     * history model, please set it false.
     */
    public $passwordHistoryClass = false;
    
    public function init()
    {
        $this->on(self::$eventAfterRegister, [$this, 'onAddPasswordToHistory'], $this->{$this->passwordHashAttribute});
        parent::init();
    }

    /**
     * Create profile.
     * If profile of this user exists, it will be returned instead of creating it.
     * Meanwhile, the $config parameter will be skipped.
     * @param array $config Profile configuration. Skipped if it exists.
     * @return Profile
     */
    public function createProfile($config = [])
    {
        $profileClass = $this->profileClass;
        if (empty($profileClass) || !is_string($this->profileClass)) {
            return null;
        }
        $profile = $profileClass::findOne($this->getGUID());
        if (!$profile) {
            $profile = $this->create($profileClass, $config);
            $profile->setGUID($this->getGUID());
        }
        return $profile;
    }

    /**
     * Get Profile query.
     * If you want to get profile model, please access this method in magic property way,
     * like following:
     * 
     * ```php
     * $user->profile;
     * ```
     * 
     * @return BaseBlameableQuery
     */
    public function getProfile()
    {
        $profileClass = $this->profileClass;
        if ($this->profileClass === false || !is_string($this->profileClass) || !class_exists($this->profileClass)) {
            return null;
        }
        $profileModel = $profileClass::buildNoInitModel();
        return $this->hasOne($profileClass, [$profileModel->createdByAttribute => $this->guidAttribute])->inverseOf('user');
    }
    
    /**
     * 
     * @param \yii\base\ModelEvent $event
     */
    public function onAddPasswordToHistory($event)
    {
        $passHash = $event->data;
        $sender = $event->sender;
        /* @var $sender static */
        if (empty($sender->passwordHistoryClass) || !class_exists($sender->passwordHistoryClass)) {
            return false;
        }
        $p = $sender->create($sender->passwordHistoryClass, [$sender->passwordHashAttribute => $passHash]);
        return $p->save();
    }

    /**
     * Add password to history.
     * Note: Please specify password history model before using this method.
     *
     * @param string $password the password to be added.
     * @return boolean whether the password added.
     * @throws \yii\base\InvalidConfigException throw if password history class not specified.
     * @throws \yii\base\InvalidParamException throw if password existed.
     */
    public function addPasswordHistory($password)
    {
        if (!empty($this->passwordHistoryClass) && class_exists($this->passwordHistoryClass)) {
            $class = $this->passwordHistoryClass;
            return $class::add($password, $this);
        }
        return false;
    }
}
