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

namespace rhosocial\user\migrations;

use rhosocial\user\User;
use rhosocial\user\Profile;

/**
 * Create Profile Table.
 *
 * This migration is equivalent to:
```SQL
CREATE TABLE `profile` (
    `guid` varbinary(16) NOT NULL COMMENT 'User GUID',
    `nickname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nickname',
    `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'First Name',
    `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Last Name',
    `gravatar_type` smallint(6) NOT NULL DEFAULT '0',
    `gravatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `gender` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Gender',
    `timezone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'UTC',
    `individual_sign` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Individual Sign',
    `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Created At',
    `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Updated At',
    PRIMARY KEY (`guid`),
    KEY `user_nickname_normal` (`nickname`) USING BTREE,
    KEY `user_first_name_normal` (`first_name`) USING BTREE,
    KEY `user_last_name_normal` (`last_name`) USING BTREE,
    KEY `user_gender_normal` (`gender`) USING BTREE,
    KEY `user_timezone_normal` (`timezone`) USING BTREE,
    KEY `user_profile_created_at_normal` (`created_at`) USING BTREE,
    CONSTRAINT `user_profile_fk` FOREIGN KEY (`guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Profile';
```SQL
 *
 * If you want to go back, please execute `yii migrate/to rhosocial\user\migrations\M170304142349CreateProfileTable`,
 * instead of droping profile table yourself.
 * Note: this execution will reverse all the migrations after this migration.
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class M170304142349CreateProfileTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Profile'";
            $this->createTable(Profile::tableName(), [
                'guid' => $this->varbinary(16)->notNull()->comment('User GUID'),
                'nickname' => $this->varchar(255)->notNull()->comment('Nickname'),
                'first_name' => $this->varchar(255)->notNull()->defaultValue('')->comment('First Name'),
                'last_name' => $this->varchar(255)->notNull()->defaultValue('')->comment('Last Name'),
                'gravatar_type' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Gravatar Type'),
                'gravatar'=> $this->varchar(255)->notNull()->defaultValue('')->comment('Gravatar'),
                'gender' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('Gender'),
                'timezone' => $this->varchar(255)->charset('utf8')->collate('utf8_unicode_ci')->notNull()->defaultValue('UTC')->comment('Timezone'),
                'individual_sign' => $this->text()->notNull()->comment('Individual Sign'),
                'created_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Created At'),
                'updated_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Updated At'),
            ], $tableOptions);
        }
        $this->addPrimaryKey('user_guid_profile_pk', Profile::tableName(), 'guid');
        $this->addForeignKey('user_profile_fk', Profile::tableName(), 'guid', User::tableName(), 'guid', 'CASCADE', 'CASCADE');
        $this->createIndex('user_nickname_normal', Profile::tableName(), 'nickname');
        $this->createIndex('user_first_name_normal', Profile::tableName(), 'first_name');
        $this->createIndex('user_last_name_normal', Profile::tableName(), 'last_name');
        $this->createIndex('user_gender_normal', Profile::tableName(), 'gender');
        $this->createIndex('user_timezone_normal', Profile::tableName(), 'timezone');
        $this->createIndex('user_profile_created_at_normal', Profile::tableName(), 'created_at');
    }

    public function down()
    {
        $this->dropTable(Profile::tableName());
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
