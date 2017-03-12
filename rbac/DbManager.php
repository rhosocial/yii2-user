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

namespace rhosocial\user\rbac;

use rhosocial\user\User;
use yii\db\Query;
use yii\rbac\Item;

/**
 * This DbManager replaces the UserID of original DbManager with the UserGUID.
 *
 * @see User
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class DbManager extends \yii\rbac\DbManager
{
    /**
     * @inheritdoc
     */
    protected function addItem($item)
    {
        $time = date('Y-m-d H:i:s');
        if ($item->createdAt === null) {
            $item->createdAt = $time;
        }
        if ($item->updatedAt === null) {
            $item->updatedAt = $time;
        }
        $this->db->createCommand()
            ->insert($this->itemTable, [
                'name' => $item->name,
                'type' => $item->type,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'created_at' => $item->createdAt,
                'updated_at' => $item->updatedAt,
            ])->execute();

        $this->invalidateCache();

        return true;
    }
    
    /**
     * @inheritdoc
     */
    protected function updateItem($name, $item)
    {
        if ($item->name !== $name && !$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemChildTable, ['parent' => $item->name], ['parent' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->itemChildTable, ['child' => $item->name], ['child' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->assignmentTable, ['item_name' => $item->name], ['item_name' => $name])
                ->execute();
        }

        $item->updatedAt = date('Y-m-d H:i:s');

        $this->db->createCommand()
            ->update($this->itemTable, [
                'name' => $item->name,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'updated_at' => $item->updatedAt,
            ], [
                'name' => $name,
            ])->execute();

        $this->invalidateCache();

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function addRule($rule)
    {
        $time = date('Y-m-d H:i:s');
        if ($rule->createdAt === null) {
            $rule->createdAt = $time;
        }
        if ($rule->updatedAt === null) {
            $rule->updatedAt = $time;
        }
        $this->db->createCommand()
            ->insert($this->ruleTable, [
                'name' => $rule->name,
                'data' => serialize($rule),
                'created_at' => $rule->createdAt,
                'updated_at' => $rule->updatedAt,
            ])->execute();

        $this->invalidateCache();

        return true;
    }
    
    /**
     * @inheritdoc
     */
    protected function updateRule($name, $rule)
    {
        if ($rule->name !== $name && !$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemTable, ['rule_name' => $rule->name], ['rule_name' => $name])
                ->execute();
        }

        $rule->updatedAt = date('Y-m-d H:i:s');

        $this->db->createCommand()
            ->update($this->ruleTable, [
                'name' => $rule->name,
                'data' => serialize($rule),
                'updated_at' => $rule->updatedAt,
            ], [
                'name' => $name,
            ])->execute();

        $this->invalidateCache();

        return true;
    }
    
    /**
     * 
     * @param string|User $userGuid
     * @return array
     */
    public function getRolesByUser($userGuid) {
        if (!isset($userGuid) || $userGuid === '') {
            return [];
        }
        
        if ($userGuid instanceof User) {
            $userGuid = $userGuid->getGUID();
        }

        $query = (new Query)->select('b.*')
            ->from(['a' => $this->assignmentTable, 'b' => $this->itemTable])
            ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
            ->andWhere(['a.user_guid' => (string) $userGuid])
            ->andWhere(['b.type' => Item::TYPE_ROLE]);

        $roles = [];
        foreach ($query->all($this->db) as $row) {
            $roles[$row['name']] = $this->populateItem($row);
        }
        return $roles;
    }

    /**
     * Returns all permissions that are directly assigned to user.
     * @param string|User $userGuid the user GUID (see [[\rhosocial\user\User::guid]])
     * @return Permission[] all direct permissions that the user has. The array is indexed by the permission names.
     */
    protected function getDirectPermissionsByUser($userGuid)
    {
        $query = (new Query)->select('b.*')
            ->from(['a' => $this->assignmentTable, 'b' => $this->itemTable])
            ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
            ->andWhere(['a.user_guid' => (string) $userGuid])
            ->andWhere(['b.type' => Item::TYPE_PERMISSION]);

        $permissions = [];
        foreach ($query->all($this->db) as $row) {
            $permissions[$row['name']] = $this->populateItem($row);
        }
        return $permissions;
    }

    /**
     * Returns all permissions that the user inherits from the roles assigned to him.
     * @param string|User $userGuid the user GUID (see [[\rhosocial\user\User::guid]])
     * @return Permission[] all inherited permissions that the user has. The array is indexed by the permission names.
     */
    protected function getInheritedPermissionsByUser($userGuid)
    {
        $query = (new Query)->select('item_name')
            ->from($this->assignmentTable)
            ->where(['user_guid' => (string) $userGuid]);

        $childrenList = $this->getChildrenList();
        $result = [];
        foreach ($query->column($this->db) as $roleName) {
            $this->getChildrenRecursive($roleName, $childrenList, $result);
        }

        if (empty($result)) {
            return [];
        }

        $query = (new Query)->from($this->itemTable)->where([
            'type' => Item::TYPE_PERMISSION,
            'name' => array_keys($result),
        ]);
        $permissions = [];
        foreach ($query->all($this->db) as $row) {
            $permissions[$row['name']] = $this->populateItem($row);
        }
        return $permissions;
    }

    /**
     * @inheritdoc
     */
    public function getAssignment($roleName, $userGuid)
    {
        if (empty($userGuid)) {
            return null;
        }
        
        if ($userGuid instanceof User) {
            $userGuid = $userGuid->getGUID();
        }

        $row = (new Query)->from($this->assignmentTable)
            ->where(['user_guid' => (string) $userGuid, 'item_name' => $roleName])
            ->one($this->db);

        if ($row === false) {
            return null;
        }

        return new Assignment([
            'userGuid' => $row['user_guid'],
            'roleName' => $row['item_name'],
            'createdAt' => $row['created_at'],
            'failedAt' => $row['failed_at'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAssignments($userGuid)
    {
        if (empty($userGuid)) {
            return [];
        }
        
        if ($userGuid instanceof User) {
            $userGuid = $userGuid->getGUID();
        }

        $query = (new Query)
            ->from($this->assignmentTable)
            ->where(['user_guid' => (string) $userGuid]);

        $assignments = [];
        foreach ($query->all($this->db) as $row) {
            $assignments[$row['item_name']] = new Assignment([
                'userGuid' => $row['user_guid'],
                'roleName' => $row['item_name'],
                'createdAt' => $row['created_at'],
                'failedAt' => $row['failed_at'],
            ]);
        }

        return $assignments;
    }

    /**
     * @inheritdoc
     */
    public function assign($role, $userGuid, $failedAt = null)
    {
        $assignment = new Assignment([
            'userGuid' => $userGuid,
            'roleName' => $role->name,
            'createdAt' => date('Y-m-d H:i:s'),
            'failedAt' => empty($failedAt) ? null : $failedAt,
        ]);

        $this->db->createCommand()
            ->insert($this->assignmentTable, [
                'user_guid' => $assignment->userGuid,
                'item_name' => $assignment->roleName,
                'created_at' => $assignment->createdAt,
                'failed_at' => $assignment->failedAt,
            ])->execute();

        return $assignment;
    }

    /**
     * @inheritdoc
     */
    public function revoke($role, $userGuid)
    {
        if (empty($userGuid)) {
            return false;
        }
        
        if ($userGuid instanceof User) {
            $userGuid = $userGuid->getGUID();
        }

        return $this->db->createCommand()
            ->delete($this->assignmentTable, ['user_guid' => (string) $userGuid, 'item_name' => $role->name])
            ->execute() > 0;
    }

    /**
     * @inheritdoc
     */
    public function revokeAll($userGuid)
    {
        if (empty($userGuid)) {
            return false;
        }
        
        if ($userGuid instanceof User) {
            $userGuid = $userGuid->getGUID();
        }

        return $this->db->createCommand()
            ->delete($this->assignmentTable, ['user_guid' => (string) $userGuid])
            ->execute() > 0;
    }

    /**
     * Returns all role assignment information for the specified role.
     * @param string $roleName
     * @return Assignment[] the assignments. An empty array will be
     * returned if role is not assigned to any user.
     * @since 2.0.7
     */
    public function getUserGuidsByRole($roleName)
    {
        if (empty($roleName)) {
            return [];
        }

        return (new Query)->select('[[user_guid]]')
            ->from($this->assignmentTable)
            ->where(['item_name' => $roleName])->column($this->db);
    }
}