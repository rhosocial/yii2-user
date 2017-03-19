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

namespace rhosocial\user\rbac\migrations;

use rhosocial\user\migrations\Migration;
use rhosocial\user\rbac\roles\Admin;
use rhosocial\user\rbac\roles\User;
use rhosocial\user\rbac\rules\CreateUserRule;
use rhosocial\user\rbac\rules\DeleteMyselfRule;
use rhosocial\user\rbac\rules\DeleteUserRule;
use rhosocial\user\rbac\rules\UpdateMyselfRule;
use rhosocial\user\rbac\rules\UpdateUserRule;
use rhosocial\user\rbac\permissions\CreateAdminUser;
use rhosocial\user\rbac\permissions\CreateUser;
use rhosocial\user\rbac\permissions\DeleteAdminUser;
use rhosocial\user\rbac\permissions\DeleteMyself;
use rhosocial\user\rbac\permissions\DeleteUser;
use rhosocial\user\rbac\permissions\UpdateAdminUser;
use rhosocial\user\rbac\permissions\UpdateMyself;
use rhosocial\user\rbac\permissions\UpdateUser;
use Yii;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

/**
 * Create following four tables in order:
 * `{{%auth_rule}}`
 * `{{%auth_item}}`
 * `{{%auth_item_child}}`
 * `{{%auth_assignment}}`
 *
 ```SQL
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Rule Name',
  `data` blob,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Auth Rule';

CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci COMMENT 'Description',
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Rule Name',
  `data` blob,
  `color` int(11) NOT NULL DEFAULT '-1' COMMENT 'Color',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name_fk` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `rule_name_fk` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Auth Item';

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child_name_fk` (`child`),
  CONSTRAINT `child_name_fk` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `parent_name_fk` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Auth Item Child';

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_guid` varbinary(16) NOT NULL,
  `created_at` datetime NOT NULL,
  `failed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_guid`),
  KEY `user_assignment_fk` (`user_guid`),
  CONSTRAINT `user_assignment_fk` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Auth Assignment';
```
 *
 * @codeCoverageIgnore
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class M170310150337CreateAuthTables extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }
    
    public function up()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

            $this->createTable($authManager->ruleTable, [
                'name' => $this->varchar(64)->notNull()->comment('Rule Name'),
                'data' => $this->blob(),
                'created_at' => $this->dateTime()->notNull(),
                'updated_at' => $this->dateTime()->notNull(),
            ], $tableOptions . " COMMENT 'Auth Rule'");
            $this->addPrimaryKey('rule_name_pk', $authManager->ruleTable, 'name');

            $this->createTable($authManager->itemTable, [
                'name' => $this->varchar(64)->notNull(),
                'type' => $this->smallInteger()->notNull(),
                'description' => $this->text()->comment('Description'),
                'rule_name' => $this->varchar(64)->comment('Rule Name'),
                'data' => $this->blob(),
                'color' => $this->integer()->defaultValue(-1)->notNull()->comment('Color'),
                'created_at' => $this->dateTime()->notNull(),
                'updated_at' => $this->dateTime()->notNull(),
            ], $tableOptions . " COMMENT 'Auth Item'");
            $this->addPrimaryKey('item_name_pk', $authManager->itemTable, 'name');
            $this->addForeignKey('rule_name_fk', $authManager->itemTable, 'rule_name', $authManager->ruleTable, 'name', 'CASCADE', 'CASCADE');
            $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');

            $this->createTable($authManager->itemChildTable, [
                'parent' => $this->varchar(64)->notNull(),
                'child' => $this->varchar(64)->notNull(),
            ], $tableOptions . " COMMENT 'Auth Item Child'");
            $this->addPrimaryKey('parent_child_pk', $authManager->itemChildTable, ['parent', 'child']);
            $this->addForeignKey('parent_name_fk', $authManager->itemChildTable, 'parent', $authManager->itemTable, 'name', 'CASCADE', 'CASCADE');
            $this->addForeignKey('child_name_fk', $authManager->itemChildTable, 'child', $authManager->itemTable, 'name', 'CASCADE', 'CASCADE');

            $this->createTable($authManager->assignmentTable, [
                'item_name' => $this->varchar(64)->notNull(),
                'user_guid' => $this->varbinary(16)->notNull(),
                'created_at' => $this->dateTime()->notNull(),
                'failed_at' => $this->dateTime(),
            ], $tableOptions . " COMMENT 'Auth Assignment'");
            $this->addPrimaryKey('user_item_name_pk', $authManager->assignmentTable, ['item_name', 'user_guid']);
            $this->addForeignKey('user_assignment_fk', $authManager->assignmentTable, 'user_guid', '{{%user}}', 'guid', 'CASCADE', 'CASCADE');
        }
        $this->addRules();
        $this->addRoles();
    }

    public function down()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;
        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);
    }
    
    protected function addRules()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;
        
        $createUserRule = new CreateUserRule();
        $deleteMyselfRule = new DeleteMyselfRule();
        $deleteUserRule = new DeleteUserRule();
        $updateMyselfRule = new UpdateMyselfRule();
        $updateUserRule = new UpdateUserRule();
        
        $authManager->add($createUserRule);
        $authManager->add($deleteMyselfRule);
        $authManager->add($deleteUserRule);
        $authManager->add($updateMyselfRule);        
        $authManager->add($updateUserRule);
    }
    
    protected function addRoles()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;
        
        $createUser = new CreateUser();
        $updateUser = new UpdateUser();
        $deleteUser = new DeleteUser();
        $updateMyself = new UpdateMyself();
        $deleteMyself = new DeleteMyself();
        $createAdminUser = new CreateAdminUser();
        $updateAdminUser = new UpdateAdminUser();
        $deleteAdminUser = new DeleteAdminUser();
        
        $authManager->add($createUser);
        $authManager->add($updateUser);
        $authManager->add($deleteUser);
        $authManager->add($updateMyself);
        $authManager->add($deleteMyself);
        $authManager->add($createAdminUser);
        $authManager->add($updateAdminUser);
        $authManager->add($deleteAdminUser);
        
        $admin = new Admin();
        $user = new User();
        
        $authManager->add($admin);
        $authManager->add($user);
        
        $authManager->addChild($user, $updateMyself);
        $authManager->addChild($user, $deleteMyself);
        $authManager->addChild($admin, $user);
        
        $authManager->addChild($admin, $createUser);
        $authManager->addChild($admin, $updateUser);
        $authManager->addChild($admin, $deleteUser);
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
