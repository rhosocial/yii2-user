<?php

/**
 *   _   __ __ _____ _____ ___  ____  _____
 *  | | / // // ___//_  _//   ||  __||_   _|
 *  | |/ // /(__  )  / / / /| || |     | |
 *  |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2017 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\migrations;

/**
 * Create Password History Table.
 *
 * This migration is equivalent to:
```SQL
CREATE TABLE `password_history` (
  `guid` varbinary(16) NOT NULL COMMENT 'Password GUID',
  `user_guid` varbinary(16) NOT NULL COMMENT 'Created By',
  `created_at` datetime NOT NULL COMMENT 'Created At',
  `pass_hash` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Password Hash',
  PRIMARY KEY (`guid`),
  KEY `user_password_fk` (`user_guid`),
  CONSTRAINT `user_password_fk` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Password History'
```
 *
 * If you want to go back, please execute `yii migrate/down rhosocial\user\migrations\M170307150614CreatePasswordHistoryTable`,
 * instead of droping password history table yourself.
 *
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class M170307150614CreatePasswordHistoryTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->getDb()->driverName == 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Password History'";
            $this->createTable('{{%password_history}}', [
                'guid' => $this->varbinary(16)->notNull()->comment('Password GUID'),
                'user_guid' => $this->varbinary(16)->notNull()->comment('Created By'),
                'created_at' => $this->dateTime()->notNull()->comment('Created At'),
                'pass_hash' => $this->varchar(80)->notNull()->comment('Password Hash'),
            ], $tableOptions);
        }
        $this->addPrimaryKey('password_guid_pk', '{{%password_history}}', 'guid');
        $this->addForeignKey('user_password_fk', '{{%password_history}}', 'user_guid', '{{%user}}', 'guid', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{$password_history}}');
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
