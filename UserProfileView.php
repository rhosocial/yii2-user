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

use Yii;

/**
 * This is the model class for view "{{%userprofileview}}".
 * That is, you can only use this model to read.
 *
 * @property resource $guid
 * @property string $id
 * @property string $pass_hash
 * @property resource $ip
 * @property integer $ip_type
 * @property string $created_at
 * @property string $updated_at
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_reset_token
 * @property integer $status
 * @property integer $type
 * @property string $source
 * @property string $nickname
 * @property string $first_name
 * @property string $last_name
 * @property integer $gravatar_type
 * @property string $gravatar
 * @property integer $gender
 * @property string $timezone
 * @property string $individual_sign
 * @property string $profile_created_at
 * @property string $profile_updated_at
 */
class UserProfileView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%userprofileview}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['guid', 'id', 'pass_hash', 'nickname', 'individual_sign'], 'required'],
            [['ip_type', 'status', 'type', 'gravatar_type', 'gender'], 'integer'],
            [['created_at', 'updated_at', 'profile_created_at', 'profile_updated_at'], 'safe'],
            [['individual_sign'], 'string'],
            [['guid', 'id', 'ip'], 'string', 'max' => 16],
            [['pass_hash'], 'string', 'max' => 80],
            [['auth_key', 'access_token', 'password_reset_token', 'source', 'nickname', 'first_name', 'last_name', 'gravatar', 'timezone'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'guid' => Yii::t('user', 'GUID'),
            'id' => Yii::t('user', 'User ID'),
            'pass_hash' => Yii::t('user', 'Password Hash'),
            'ip' => Yii::t('user', 'IP Address'),
            'ip_type' => Yii::t('user', 'IP Address Type'),
            'created_at' => Yii::t('user', 'Created At'),
            'updated_at' => Yii::t('user', 'Updated At'),
            'auth_key' => Yii::t('user', 'Authentication Key'),
            'access_token' => Yii::t('user', 'Access Token'),
            'password_reset_token' => Yii::t('user', 'Password Reset Token'),
            'status' => Yii::t('user', 'Status'),
            'type' => Yii::t('user', 'Type'),
            'source' => Yii::t('user', 'Source'),
            'nickname' => Yii::t('user', 'Nickname'),
            'first_name' => Yii::t('user', 'First Name'),
            'last_name' => Yii::t('user', 'Last Name'),
            'gravatar_type' => Yii::t('user', 'Gravatar Type'),
            'gravatar' => Yii::t('user', 'Gravatar'),
            'gender' => Yii::t('user', 'Gender'),
            'timezone' => Yii::t('user', 'Timezone'),
            'individual_sign' => Yii::t('user', 'Individual Sign'),
            'profile_created_at' => Yii::t('user', 'Profile Created At'),
            'profile_updated_at' => Yii::t('user', ' Profile Updated At'),
        ];
    }

    public function delete() {
        return false;
    }

    public function insert($runValidation = true, $attributes = null) {
        return false;
    }

    public function update($runValidation = true, $attributeNames = null) {
        return false;
    }

    public function save($runValidation = true, $attributeNames = null) {
        return false;
    }
}
