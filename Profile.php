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
 * @property string $nickname
 * @property string $first_name
 * @property string $last_name
 * @property string $individual_sign
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge($this->getNameRules(), $this->getIndividualSignRules(), parent::rules());
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }
}