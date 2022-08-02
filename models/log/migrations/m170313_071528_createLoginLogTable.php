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

namespace rhosocial\user\models\log\migrations;

use rhosocial\user\User;
use rhosocial\user\models\log\LoginLog;
use rhosocial\user\migrations\Migration;

/**
 * Create Login Log Table.
 * This migration is equivalent to:
```SQL
CREATE TABLE `log_login` (
    `guid` varbinary(16) NOT NULL COMMENT 'Login Log GUID',
    `id` varchar(4) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Login Log ID',
    `user_guid` varbinary(16) NOT NULL COMMENT 'User GUID',
    `ip` varbinary(16) NOT NULL DEFAULT '0' COMMENT 'IP Address',
    `ip_type` smallint(6) NOT NULL DEFAULT '4' COMMENT 'IP Address Type',
    `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Login Time',
    `status` int(11) NOT NULL DEFAULT '0',
    `device` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`guid`),
    UNIQUE KEY `login_log_id_unique` (`guid`,`id`),
    KEY `login_log_creator_fk` (`user_guid`),
    KEY `login_log_created_at_normal` (`created_at`) USING BTREE,
    KEY `login_log_status_normal` (`status`) USING BTREE,
    KEY `login_log_device_normal` (`device`) USING BTREE,
    CONSTRAINT `login_log_creator_fk` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Login Log';
```
 * @codeCoverageIgnore
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class m170313_071528_createLoginLogTable extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = "CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ENGINE=InnoDB COMMENT='Login Log'";
            $this->createTable(LoginLog::tableName(), [
                'guid' => $this->varbinary(16)->notNull()->comment('Login Log GUID'),
                'id' => $this->varchar(4)->notNull()->comment('Login Log ID'),
                'user_guid' => $this->varbinary(16)->notNull()->comment('User GUID'),
                'ip' => $this->varbinary(16)->defaultValue(0)->notNull()->comment('IP Address'),
                'ip_type' => $this->smallInteger()->defaultValue(4)->notNull()->comment('IP Address Type'),
                'created_at' => $this->dateTime()->defaultValue('1970-01-01 00:00:00')->notNull()->comment('Login Time'),
                'status' => $this->integer()->defaultValue(0)->notNull(),
                'device' => $this->integer()->defaultValue(0)->notNull(),
            ], $tableOptions);
        }
        $this->addPrimaryKey('login_log_pk', LoginLog::tableName(), 'guid');
        $this->createIndex('login_log_id_unique', LoginLog::tableName(), ['guid', 'id'], true);
        $this->createIndex('login_log_created_at_normal', LoginLog::tableName(), 'created_at');
        $this->createIndex('login_log_status_normal', LoginLog::tableName(), 'status');
        $this->createIndex('login_log_device_normal', LoginLog::tableName(), 'device');
        $this->addForeignKey('login_log_creator_fk', LoginLog::tableName(), 'user_guid', User::tableName(), 'guid', 'CASCADE', 'CASCADE');
        return true;
    }

    public function safeDown()
    {
        $this->dropTable(LoginLog::tableName());
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
