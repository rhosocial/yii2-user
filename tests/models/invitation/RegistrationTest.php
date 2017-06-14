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

namespace rhosocial\user\tests\models\invitation;

use rhosocial\user\tests\data\User;
use rhosocial\user\tests\TestCase;

/**
 * Class RegistrationTest
 * @package rhosocial\user\tests\models\invitation
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class RegistrationTest extends TestCase
{
    protected $user;

    protected $invitee;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->user = new User(['password' => '123456']);
        $this->assertTrue($this->user->register());
    }

    protected function tearDown()
    {
        if ($this->user && !$this->user->getIsNewRecord()) {
            $this->user->deregister();
        }
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    /**
     * @group invitation
     * @group register
     */
    public function testEmpty()
    {
        $this->assertInstanceOf(User::class, $this->user);
        $this->assertEmpty($this->user->invitationRegistrations);
    }
}
