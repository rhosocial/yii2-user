<?php

/**
 *   _   __ __ _____ _____ ___  ____  _____
 *  | | / // // ___//_  _//   ||  __||_   _|
 *  | |/ // /(__  )  / / / /| || |     | |
 *  |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2017 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\tests\profile;

use rhosocial\user\tests\data\User;
use rhosocial\user\tests\data\Profile;
use rhosocial\user\tests\TestCase;

class NormalTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;
    
    /**
     * @var Profile
     */
    protected $profile;
    
    protected function setUp() {
        parent::setUp();
        $this->user = new User(['password' => '123456']);
        $this->profile = $this->user->createProfile(['nickname' => 'vistart']);
    }
    
    protected function tearDown()
    {
        if ($this->user instanceof User) {
            try {
                $this->user->deregister();
            } catch (\Exception $ex) {

            }
            $this->user =  null;
            $this->profile = null;
        }
        Profile::deleteAll();
        User::deleteAll();
        parent::tearDown();
    }
    
    /**
     * @group profile
     */
    public function testClassName()
    {
        $this->assertEquals(Profile::class, $this->user->profileClass);
    }
    
    /**
     * @group profile
     */
    public function testNew()
    {
        $this->assertNull(Profile::findOne($this->user->getGUID()));
        $this->assertNull(Profile::findOne($this->profile->getGUID()));
        $this->assertInstanceOf(get_class(Profile::find()), $this->user->getProfile());
        $this->assertNull($this->user->profile);
        
        $this->assertTrue($this->user->register([$this->profile]));
        
        $this->assertInstanceOf(Profile::class, Profile::findOne($this->user->getGUID()));
        $this->assertInstanceOf(Profile::class, Profile::findOne($this->profile->getGUID()));
        unset($this->user->profile);
        $this->assertInstanceOf(Profile::class, $this->user->profile);
        
        $this->assertTrue($this->user->deregister());
        
        $this->assertNull(Profile::findOne($this->user->getGUID()));
        $this->assertNull(Profile::findOne($this->profile->getGUID()));
        $this->assertInstanceOf(get_class(Profile::find()), $this->user->getProfile());
        $this->assertInstanceOf(Profile::class, $this->user->profile);
    }
}