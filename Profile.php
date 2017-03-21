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

/**
 * Simple Profile Model.
 * One Profile corresponds to only one [[User]].
 *
 * If you're using MySQL, we recommend that you create a data table using the following statement:
 * ```
 * CREATE TABLE `profile` (
 *   `guid` varbinary(16) NOT NULL COMMENT 'User GUID',
 *   `nickname` varchar(255) NOT NULL COMMENT 'Nickname',
 *   `first_name` varchar(255) NOT NULL COMMENT 'First Name',
 *   `last_name` varchar(255) NOT NULL COMMENT 'Last Name',
 *   `individual_sign` text NOT NULL COMMENT 'Individual Sign',
 *   `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Create Time',
 *   `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Update Time',
 *   PRIMARY KEY (`guid`),
 *   CONSTRAINT `user_profile_fkey` FOREIGN KEY (`guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Profile';
 * ```
 *
 * @property string $nickname Nickname
 * @property string $first_name First Name
 * @property string $last_name Last Name
 * @property string $gender Gender
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
            ['individual_sign', 'default', 'value' => true],
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
            [['first_name', 'last_name'], 'default', 'value' => true],
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
}
