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

namespace rhosocial\user\tests\security;

use rhosocial\user\tests\TestCase;
use rhosocial\user\tests\data\User;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class PasswordHistoryTest extends TestCase
{
    protected $user;
    
    protected function setUp() {
        parent::setUp();
        $this->user = new User(['password' => '123456']);
    }
}