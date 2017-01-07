<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user;

use rhosocial\base\models\models\BaseBlameableModel;

/**
 * Common Profile Model.
 * One Profile corresponds to only one [[User]].
 * 
 * If you're using MySQL, we recommend that you create a data table using the following statement:
 * ```
 * CREATE TABLE `profile` (
 *   `guid` varchar(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User GUID',
 *   `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nickname',
 *   `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Email',
 *   `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Phone',
 *   `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'First Name',
 *   `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Last Name',
 *   `individual_sign` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Individual Sign',
 *   `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Create Time',
 *   `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Update Time',
 *   PRIMARY KEY (`guid`),
 *   CONSTRAINT `user_profile_fkey` FOREIGN KEY (`guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Profile';
 * ```
 * 
 * The fields of Profile table in database are following:
 * @property string $guid Profile GUID, which is same as the User's.
 * @property string $nickname
 * @property string $email
 * @property string $phone
 * @property string $first_name
 * @property string $last_name
 * @property string $individual_sign
 * @property string $create_time
 * @property string $update_time
 * 
 * @property-read User $user
 *
 * @author vistart <i@vistart.me>
 */
class Profile extends BaseBlameableModel
{

    public $createdByAttribute = 'guid';
    public $updatedByAttribute = false;
    public $idAttribute = false;
    public $enableIP = 0;

    /**
     * @var string Specify the nickname as the content attribute.
     */
    public $contentAttribute = 'nickname';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->userClass) || !class_exists($this->userClass)) {
            if (class_exists(__NAMESPACE__ . '\User')) {
                $this->userClass = __NAMESPACE__ . '\User';
            } else {
                $this->userClass = User::className();
            }
        }
        parent::init();
    }

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
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge($this->getEmailRules(), $this->getPhoneRules(), $this->getNameRules(), $this->getIndividualSignRules(), parent::rules());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }
}
