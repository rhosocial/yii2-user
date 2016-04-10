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
class RegisterUserTest extends TestCase
{

    public function testNew()
    {
        $user = new User();
        $this->assertInstanceOf(User::className(), $user);

        $profile = $user->createProfile();
        $this->assertNull($profile);

        $this->assertNull($user->profile);
    }

    public function testRegister($user = null, $associatedModels = null)
    {
        if (empty($user)) {
            $user = new User(['password' => '123456']);
        }
        $result = $user->register($associatedModels);
        if ($result === true) {
            $this->assertTrue(true);
        } else {
            $this->fail($result);
        }

        $user = User::findOne($user->guid);

        if ($user->deregister()) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }

    public function testProfile()
    {
        $user = new User(['profileClass' => true, 'password' => 123456]);
        $this->assertInstanceOf(User::className(), $user);

        $profile = $user->createProfile(['nickname' => 'vistart']);
        $this->assertInstanceOf(\rhosocial\user\Profile::className(), $profile);
        $this->assertEquals($user->guid, $profile->guid);
        $this->assertNull($user->profile);

        $result = $user->register([$profile]);
        if ($result === true) {
            $this->assertTrue(true);
        } else {
            $this->fail($result);
        }
        
        unset($user->profile);
        
        $this->assertInstanceOf(\rhosocial\user\Profile::className(), $user->profile);


        if ($user->deregister()) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }
}
