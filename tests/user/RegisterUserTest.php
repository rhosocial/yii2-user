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

namespace rhosocial\user\tests\user;

use rhosocial\user\tests\TestCase;
use rhosocial\user\tests\data\User;
use rhosocial\user\tests\data\Profile;

/**
 * Description of RegisterUserTest
 *
 * @author vistart <i@vistart.me>
 */
class RegisterUserTest extends TestCase
{
    /**
     * @group register
     */
    public function testNew()
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);

        $profile = $user->createProfile();
        $this->assertInstanceOf(Profile::class, $profile);

        $this->assertNull($user->profile);
    }

    /**
     * @group register
     * @param type $user
     * @param type $associatedModels
     */
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

    /**
     * @group register
     */
    public function testProfile()
    {
        $user = new User(['profileClass' => true, 'password' => '123456']);
        $this->assertInstanceOf(User::class, $user);

        $profile = $user->createProfile(['nickname' => 'vistart']);
        $this->assertNull($profile);
        $this->assertNull($user->profile);
    }
}
