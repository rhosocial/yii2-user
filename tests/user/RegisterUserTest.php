<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license https://vistart.name/license/
 */

namespace rhosocial\user\tests\user;

use rhosocial\user\tests\TestCase;
use rhosocial\user\tests\data\User;

/**
 * Description of RegisterUserTest
 *
 * @author vistart
 */
class RegisterUserTest extends TestCase {
    
    public function testNew()
    {
        $user = new User();
        $this->assertInstanceOf(User::className(), $user);
    }

    public function testRegister() {
        $user = new User(['password' => '123456']);
        if ($user->register()) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
        
        if ($user->deregister()) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }

}
