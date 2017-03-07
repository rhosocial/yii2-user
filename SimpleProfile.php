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

/**
 * Simple Profile Model.
 * One Profile corresponds to only one [[User]].
 * 
 * If you're using MySQL, we recommend that you create a data table using the following statement:
 * ```
 * CREATE TABLE `profile` (
 *   `guid` varbinary(16) NOT NULL COMMENT 'User GUID',
 *   `nickname` varchar(255) NOT NULL COMMENT 'Nickname',
 *   `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Email',
 *   `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Phone',
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
 * The extra fields of SimpleProfile table in database are following:
 * @property string $email
 * @property string $phone
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class SimpleProfile extends Profile
{
    /**
     * Get rules associated with email attribute.
     * You can override this method if current rules do not satisfy your needs.
     * If you do not use email attribute, please override this method and return empty array.
     * @return array Rules associated with email.
     */
    public function getEmailRules()
    {
        return [
            ['email', 'email', 'skipOnEmpty' => true],
            ['email', 'default', 'value' => ''],
        ];
    }

    /**
     * Get rules associated with phone attribute.
     * You can override this method if current rules do not satisfy your needs.
     * If you do not use phone attribute, please override this method and return empty array.
     * @return array Rules associated with phone.
     */
    public function getPhoneRules()
    {
        return [
            ['phone', 'string', 'max' => 255, 'skipOnEmpty' => true],
            ['phone', 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge($this->getEmailRules(), $this->getPhoneRules(), parent::rules());
    }
}
