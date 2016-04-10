<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license https://vistart.name/license/
 */

namespace rhosocial\user;

use vistart\Models\models\BaseUserModel;
use vistart\Models\queries\BaseBlameableQuery;
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
 *   `guid` varchar(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'GUID',
 *   `id` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ID',
 *   `pass_hash` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Password Hash',
 *   `ip_1` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'IP 1',
 *   `ip_2` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'IP 2',
 *   `ip_3` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'IP 3',
 *   `ip_4` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'IP 4',
 *   `ip_type` tinyint(3) unsigned NOT NULL DEFAULT '4' COMMENT 'IP Address Type',
 *   `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Create Time',
 *   `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Update Time',
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
 *   KEY `user_create_time_normal` (`create_time`)
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
 * @property integer $ip_1 The first 32-bit of IPv6 address, or IPv4 address.
 * @property integer $ip_2
 * @property integer $ip_3
 * @property integer $ip_4
 * @property integer $ip_type
 * @property string $create_time
 * @property string $update_time
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
 * 
 * ```php
 * $profile = $user->profile;
 * $profile->nickname = 'vistart';
 * $profile->save();
 * ```
 *
 * @author vistart <i@vistart.name>
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
            'ip_1' => Yii::t('app', 'IP 1'),
            'ip_2' => Yii::t('app', 'IP 2'),
            'ip_3' => Yii::t('app', 'IP 3'),
            'ip_4' => Yii::t('app', 'IP 4'),
            'ip_type' => Yii::t('app', 'IP Address Type'),
            'create_time' => Yii::t('app', 'Create Time'),
            'update_time' => Yii::t('app', 'Update Time'),
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

    public function init()
    {
        if (!is_string($this->profileClass) && $this->profileClass !== false) {
            if (class_exists('Profile')) {
                $this->profileClass = 'Profile';
            } else {
                $this->profileClass = Profile::className();
            }
        }
        parent::init();
    }

    /**
     * Create or get an existed profile.
     * @param array $config
     * @return Profile
     */
    public function createProfile($config = [])
    {
        $profileClass = $this->profileClass;
        if ($this->profileClass === false || !is_string($this->profileClass)) {
            return null;
        }
        $profile = $profileClass::findOne($this->guid);
        if (!$profile) {
            $profile = $this->create($profileClass, $config);
            $profile->guid = $this->guid;
        }
        return $profile;
    }

    /**
     * Get Profile query.
     * If you want to get profile model, please access this method by magic property, like following:
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
}
