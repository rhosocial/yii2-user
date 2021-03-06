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

namespace rhosocial\user\tests\models;

use rhosocial\user\tests\data\User;
use rhosocial\user\tests\TestCase;

/**
 * Class UsernameTest
 * @package rhosocial\user\tests\models
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UsernameTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $username = 'vistart';

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        $this->user = new User(['password' => '123456']);
        $result = $this->user->register([$this->user->createUsername($this->username)]);
        if (!is_bool($result)) {
            echo $result->getMessage();
            $this->fail();
        }
        $this->assertTrue($result);
        $this->assertTrue(is_numeric($this->user->getID()));
    }

    protected function tearDown()
    {
        User::deleteAll();
        parent::tearDown();
    }

    /**
     * @group username
     */
    public function testNormal()
    {
        $this->assertEquals($this->username, $this->user->username->content);
        $this->assertEquals($this->username, (string)$this->user->username);
    }

    /**
     * @group username
     */
    public function testSet()
    {
        $this->user->username = $this->username . '1';
        $this->assertEquals($this->username . '1', (string)$this->user->getUsername()->one());
    }

    /**
     * @group username
     */
    public function testRemove()
    {
        $this->assertTrue($this->user->removeUsername());
        $this->assertNull($this->user->getUsername()->one());
        $this->assertTrue($this->user->hasEnabledUsername());
    }

    /**
     * @group username
     */
    public function testUnique()
    {
        $this->assertEquals($this->username, (string)$this->user->username);
        $user = new User(['password' => '123456']);
        try {
            $result = $user->register([$user->createUsername($this->username)]);
            if ($result instanceof \Exception) {
                throw $result;
            }
            $this->fail('Registration should be failed.');
        } catch (\Exception $ex) {
            $this->assertNotEmpty($ex->getMessage());
        }
        $user = new User(['password' => '123456']);
        $this->assertTrue($user->register([$user->createUsername($this->username . '1')]));
    }
}
