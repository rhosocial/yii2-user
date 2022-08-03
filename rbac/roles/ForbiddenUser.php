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

namespace rhosocial\user\rbac\roles;

use rhosocial\user\rbac\Role;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class ForbiddenUser extends Role
{
    /**
     * @inheritdoc
     */
    public $name = 'forbidden-user';

    /**
     * @inheritdoc
     */
    public $description = 'Forbidden User';
}