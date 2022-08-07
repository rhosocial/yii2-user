<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2022 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\models\migrations;

use rhosocial\user\models\User;

/**
 * Create User Table.
 *
 * This migration is equivalent to:
 ```SQL 
CREATE TABLE `user` (
    `guid` varbinary(16) NOT NULL COMMENT 'User GUID',
    `id` varchar(16) NOT NULL COMMENT 'User ID No.',
    `pass_hash` varchar(80) NOT NULL COMMENT 'Password Hash',
    `ip` varbinary(16) NOT NULL DEFAULT '0' COMMENT 'IP Address',
    `ip_type` tinyint NOT NULL DEFAULT '4' COMMENT 'IP Address Type',
    `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Created At',
    `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Updated At',
    `auth_key` varchar(40) NOT NULL COMMENT 'Authentication Key',
    `access_token` varchar(40) NOT NULL COMMENT 'Access Token',
    `password_reset_token` varchar(40) DEFAULT NULL COMMENT 'Password Reset Token',
    `status` tinyint NOT NULL DEFAULT '1' COMMENT 'Status',
    `type` tinyint NOT NULL DEFAULT '0' COMMENT 'Type',
    `source` varchar(255) NOT NULL DEFAULT '' COMMENT 'Source',
    PRIMARY KEY (`guid`),
    UNIQUE KEY `user_id_unique` (`id`),
    UNIQUE KEY `user_auth_key_unique` (`auth_key`),
    UNIQUE KEY `user_access_token_unique` (`access_token`),
    UNIQUE KEY `user_password_reset_token_unique` (`password_reset_token`),
    KEY `user_created_at_normal` (`created_at`),
    KEY `user_status_normal` (`status`),
    KEY `user_type_normal` (`type`),
    KEY `user_source_normal` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='User'
```
 *
 * If you want to go back, please execute `yii migrate/to rhosocial\user\models\migrations\M170304140437CreateUserTable`,
 * instead of droping user table yourself.
 * Note: this execution will reverse all the migrations after this migration.
 * 
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class M170304140437CreateUserTable extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='User'";
            $this->createTable(User::tableName(), [
                'guid' => $this->varbinary(16)->notNull()->comment('User GUID'),
                'id' => $this->varchar(16)->notNull()->comment('User ID No.'),
                'pass_hash' => $this->varchar(80)->notNull()->comment('Password Hash'),
                'ip' => $this->varbinary(16)->notNull()->defaultValue(0)->comment('IP Address'),
                'ip_type' => $this->tinyInteger(3)->unsigned()->notNull()->defaultValue(4)->comment('IP Address Type'),
                'created_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Created At'),
                'updated_at' => $this->dateTime()->notNull()->defaultValue('1970-01-01 00:00:00')->comment('Updated At'),
                'auth_key' => $this->varchar(40)->notNull()->comment('Authentication Key'),
                'access_token' => $this->varchar(40)->notNull()->comment('Access Token'),
                'password_reset_token' => $this->varchar(40)->defaultValue(null)->comment('Password Reset Token'),
                'status' => $this->tinyInteger(3)->unsigned()->notNull()->defaultValue(1)->comment('Status'),
                'type' => $this->tinyInteger(3)->unsigned()->notNull()->defaultValue(0)->comment('Type'),
                'source' => $this->varchar(255)->notNull()->defaultValue('')->comment('Source'),
            ], $tableOptions);
        }
        $this->addPrimaryKey('user_guid_pk', User::tableName(), 'guid');
        $this->createIndex('user_id_unique', User::tableName(), 'id', true);
        $this->createIndex('user_auth_key_unique', User::tableName(), 'auth_key', true);
        $this->createIndex('user_access_token_unique', User::tableName(), 'access_token', true);
        $this->createIndex('user_password_reset_token_unique', User::tableName(), 'password_reset_token', true);
        $this->createIndex('user_created_at_normal', User::tableName(), 'created_at');
        $this->createIndex('user_status_normal', User::tableName(), 'status');
        $this->createIndex('user_type_normal', User::tableName(), 'type');
        $this->createIndex('user_source_normal', User::tableName(), 'source');
        return true;
    }

    public function safeDown()
    {
        $this->dropTable(User::tableName());
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
