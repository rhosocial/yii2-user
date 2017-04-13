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

use rhosocial\base\models\models\BaseBlameableModel;
use Yii;

/**
 * Simple Profile Model.
 * One Profile corresponds to only one [[User]].
 *
 * If you're using MySQL, we recommend that you create a data table using the following statement:
```SQL
CREATE TABLE `profile` (
  `guid` varbinary(16) NOT NULL COMMENT 'User GUID',
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nickname',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'First Name',
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Last Name',
  `gravatar_type` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Gravatar Type',
  `gravatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Gravatar',
  `gender` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Gender',
  `timezone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'UTC' COMMENT 'Timezone',
  `individual_sign` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Individual Sign',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Created At',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Updated At',
  PRIMARY KEY (`guid`),
  CONSTRAINT `user_profile_fk` FOREIGN KEY (`guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Profile';
```
 *
 * @property string $nickname Nickname
 * @property string $first_name First Name
 * @property string $last_name Last Name
 * @property string $gender Gender
 * @property string $gravatar_type Gravatar Type
 * @property string $gravatar Gravatar
 * @property string $timezone Timezone
 * @property string $individual_sign Individual Signature
 *
 * @property-read User $user
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Profile extends BaseBlameableModel
{
    public $createdByAttribute = 'guid';

    // The host of Profile is only permitted to modify it.
    public $updatedByAttribute = false;

    // Profile do not have its identifier.
    public $idAttribute = false;

    // Profile do not need to record IP address.
    public $enableIP = 0;

    /**
     * @var string Specify the nickname as the content attribute.
     */
    public $contentAttribute = 'nickname';

    const SCENARIO_UPDATE = 'update';

    public function attributeLabels()
    {
        return [
            $this->contentAttribute => Yii::t('user', 'Nickname'),
            'first_name' => Yii::t('user', 'First Name'),
            'last_name' => Yii::t('user', 'Last Name'),
            'gender' => Yii::t('user', 'Gender'),
            'gravatar_type' => Yii::t('user', 'Gravatar Type'),
            'gravatar' => Yii::t('user', 'Gravatar'),
            'timezone' => Yii::t('user', 'Timezone'),
            'individual_sign' => Yii::t('user', 'Individual Signature'),
            $this->createdByAttribute => Yii::t('user', 'Created By'),
            $this->createdAtAttribute => Yii::t('user', 'Creation Time'),
            $this->updatedAtAttribute => Yii::t('user', 'Last Updated Time'),
        ];
    }

    /**
     * Get rules associated with individual sign attribute.
     * You can override this method if current rules do not satisfy your needs.
     * If you do not use individual sign attribute, please override this method and return empty array.
     * @return array Rules associated with individual sign.
     */
    public function getIndividualSignRules()
    {
        return [
            ['individual_sign', 'string', 'skipOnEmpty' => true],
            ['individual_sign', 'default', 'value' => ''],
        ];
    }

    /**
     * Get rules associated with name attribute.
     * You can override this method if current rules do not satisfy your needs.
     * If you do not use name attribute, please override this method and return empty array.
     * @return array Rules associated with name.
     */
    public function getNameRules()
    {
        return [
            [['first_name', 'last_name'], 'string', 'max' => 255, 'skipOnEmpty' => true],
            [['first_name', 'last_name'], 'default', 'value' => ''],
        ];
    }

    const GENDER_UNSPECIFIED = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    public static $genders = [
        self::GENDER_UNSPECIFIED => 'Unspecified',
        self::GENDER_MALE => 'Male',
        self::GENDER_FEMALE => 'Female',
    ];

    /**
     * Get rules associated with gender attribute.
     * You can override this method if current rules do not satisfy your needs.
     * If you do not use gender attribute, please override this method and return empty array.
     * @return array Rules associated with gender.
     */
    public function getGenderRules()
    {
        return [
            ['gender', 'default', 'value' => 0],
            ['gender', 'in', 'range' => array_keys(static::$genders)],
        ];
    }

    public static function getGenderDesc($gender = null)
    {
        if (array_key_exists($gender, self::$genders)) {
            return Yii::t('user', self::$genders[$gender]);
        }
        return null;
    }

    public static function getGenderDescs()
    {
        $genders = [];
        foreach (self::$genders as $key => $gender) {
            $genders[$key] = static::getGenderDesc($key);
        }
        return $genders;
    }

    public static function getGenderDescsWithEmpty()
    {
        return array_merge(['' => Yii::t('user', 'All')], static::getGenderDescs());
    }

    public function getGravatarRules()
    {
        return [
            ['gravatar_type', 'default', 'value' => 0],
            ['gravatar_type', 'integer'],
            ['gravatar', 'default', 'value' => ''],
            ['gravatar', 'string', 'max' => 255],
        ];
    }

    public function getTimezoneRules()
    {
        return [
            ['timezone', 'default', 'value' => 'UTC'],
            ['timezone', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge($this->getNameRules(),
                $this->getGenderRules(),
                $this->getGravatarRules(),
                $this->getTimezoneRules(),
                $this->getIndividualSignRules(),
                parent::rules());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_UPDATE => [$this->contentAttribute, 'first_name', 'last_name', 'gender', 'gravatar_type', 'gravatar', 'timezone', 'individual_sign'],
        ]);
    }
}
