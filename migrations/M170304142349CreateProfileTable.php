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
  `gender` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Gender',
  `individual_sign` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Individual Sign',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Created At',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Updated At',
  PRIMARY KEY (`guid`),
  CONSTRAINT `user_profile_fk` FOREIGN KEY (`guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Profile';
```SQL
 *
 * If you want to go back, please execute `yii migrate/down rhosocial\user\migrations\M170304142349CreateProfileTable`,
 * instead of droping profile table yourself.
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
                'first_name' => $this->varchar(255)->notNull()->comment('First Name'),
                'last_name' => $this->varchar(255)->notNull()->comment('Last Name'),
                'gender' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('Gender'),
                'individual_sign' => $this->text()->notNull()->comment('Individual Sign'),
                'created_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Created At'),
                'updated_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Updated At'),
            ], $tableOptions);
        }
        $this->addPrimaryKey('user_guid_profile_pk', Profile::tableName(), 'guid');
        $this->addForeignKey('user_profile_fk', Profile::tableName(), 'guid', User::tableName(), 'guid', 'CASCADE', 'CASCADE');
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
