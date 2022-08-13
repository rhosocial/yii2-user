<?php

/**
 *   _   __ __ _____ _____ ___  ____  _____
 *  | | / // // ___//_  _//   ||  __||_   _|
 *  | |/ // /(__  )  / / / /| || |     | |
 *  |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2022 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\models\migrations;

use rhosocial\user\models\User;
use rhosocial\user\models\security\PasswordHistory;

/**
 * Create Password History Table.
 *
 * This migration is equivalent to:
```SQL
CREATE TABLE `password_history` (
    `guid` varbinary(16) NOT NULL COMMENT 'Password GUID',
    `user_guid` varbinary(16) NOT NULL COMMENT 'Created By',
    `created_at` datetime NOT NULL COMMENT 'Created At',
    `pass_hash` varchar(80) NOT NULL COMMENT 'Password Hash',
    PRIMARY KEY (`guid`),
    KEY `user_password_fk` (`user_guid`),
    KEY `user_password_created_at_normal` (`created_at`),
    CONSTRAINT `user_password_fk` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Password History';
```
 *
 * If you want to go back, please execute `yii migrate/to rhosocial\user\models\migrations\M170307150614CreatePasswordHistoryTable`,
 * instead of droping password history table yourself.
 * Note: this execution will reverse all the migrations after this migration.
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class M170307150614CreatePasswordHistoryTable extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName == 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Password History'";
            $this->createTable(PasswordHistory::tableName(), [
                'guid' => $this->varbinary(16)->notNull()->comment('Password GUID'),
                'user_guid' => $this->varbinary(16)->notNull()->comment('Created By'),
                'created_at' => $this->dateTime()->notNull()->comment('Created At'),
                'pass_hash' => $this->varchar(80)->notNull()->comment('Password Hash'),
            ], $tableOptions);
        }
        $this->addPrimaryKey('password_guid_pk', PasswordHistory::tableName(), 'guid');
        $this->addForeignKey('password_user_guid_fk', PasswordHistory::tableName(), 'user_guid', User::tableName(), 'guid', 'CASCADE', 'CASCADE');
        $this->createIndex('user_password_created_at_index_normal', PasswordHistory::tableName(), 'created_at');
        return true;
    }

    public function safeDown()
    {
        $this->dropTable(PasswordHistory::tableName());
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function up()
    {
    }

    public function down()
    {
    }
    */
}
