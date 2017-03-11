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

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class DbManager extends \yii\rbac\DbManager
{
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
            ]);
        }

        return $assignments;
    }

    /**
     * @inheritdoc
     */
    public function assign($role, $userGuid)
    {
        $assignment = new Assignment([
            'userGuid' => $userGuid,
            'roleName' => $role->name,
            'createdAt' => time(),
        ]);

        $this->db->createCommand()
            ->insert($this->assignmentTable, [
                'user_guid' => $assignment->userGuid,
                'item_name' => $assignment->roleName,
                'created_at' => $assignment->createdAt,
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
    public function getUserIdsByRole($roleName)
    {
        if (empty($roleName)) {
            return [];
        }

        return (new Query)->select('[[user_guid]]')
            ->from($this->assignmentTable)
            ->where(['item_name' => $roleName])->column($this->db);
    }
}