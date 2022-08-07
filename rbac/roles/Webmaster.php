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

namespace rhosocial\user\rbac\roles;

use rhosocial\user\rbac\Role;

/**
 * Class Webmaster
 * @package rhosocial\user\rbac\roles
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Webmaster extends Role
{
    /**
     * @var string
     */
    public $name = 'webmaster';

    /**
     * @var string
     */
    public $description = 'Webmaster';
}
