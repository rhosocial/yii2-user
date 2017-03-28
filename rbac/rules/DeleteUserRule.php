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

namespace rhosocial\user\rbac\rules;

use rhosocial\user\User;
use rhosocial\user\rbac\permissions\DeleteUser;
use yii\rbac\Rule;

class DeleteUserRule extends Rule
{
    public $name = 'canDeleteUser';
    
    /**
     * Executes the rule.
     *
     * @param string|User $user the user GUID. This should be either a GUID string representing
     * the unique identifier of a user or a User instance. See [[\rhosocial\user\User::guid]].
     * @param DeleteUser $item the role or permission that this rule is associated with
     * @param array $params parameters passed to [[CheckAccessInterface::checkAccess()]].
     * @return bool a value indicating whether the rule permits the auth item it is associated with.
     */
    public function execute($user, $item, $params) {
        return true;
    }
}
