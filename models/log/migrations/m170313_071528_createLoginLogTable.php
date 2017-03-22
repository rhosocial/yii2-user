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
use rhosocial\user\models\log\Login;
use rhosocial\user\migrations\Migration;

/**
 * Create Login Log Table.
 * This migration is equivalent to:
```SQL
CREATE TABLE IF NOT EXISTS `log_login` (
  `guid` varbinary(16) NOT NULL,
  `id` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `user_guid` varbinary(16) NOT NULL,
  `ip` varbinary(16) NOT NULL DEFAULT '0',
  `ip_type` smallint(6) NOT NULL DEFAULT '4',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `status` int(11) NOT NULL DEFAULT '0',
  `device` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`guid`),
  UNIQUE KEY `login_log_id_unique` (`guid`,`id`),
  KEY `login_log_creator_fk` (`user_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```
 * @codeCoverageIgnore
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class m170313_071528_createLoginLogTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            $this->createTable(Login::tableName(), [
                'guid' => $this->varbinary(16)->notNull(),
                'id' => $this->varchar(4)->notNull(),
                'user_guid' => $this->varbinary(16)->notNull(),
                'ip' => $this->varbinary(16)->defaultValue(0)->notNull(),
                'ip_type' => $this->smallInteger()->defaultValue(4)->notNull(),
                'created_at' => $this->dateTime()->defaultValue('1970-01-01 00:00:00')->notNull(),
                'status' => $this->integer()->defaultValue(0)->notNull(),
                'device' => $this->integer()->defaultValue(0)->notNull(),
            ], $tableOptions);
        }
        $this->addPrimaryKey('login_log_pk', Login::tableName(), 'guid');
        $this->createIndex('login_log_id_unique', Login::tableName(), ['guid', 'id'], true);
        $this->addForeignKey('login_log_creator_fk', Login::tableName(), 'user_guid', User::tableName(), 'guid', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(Login::tableName());
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
