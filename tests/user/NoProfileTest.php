<?php

/**
 *   _   __ __ _____ _____ ___  ____  _____
 *  | | / // // ___//_  _//   ||  __||_   _|
 *  | |/ // /(__  )  / / / /| || |     | |
 *  |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2022 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\tests\user;

use rhosocial\user\tests\data\noprofile\User;
use rhosocial\user\tests\TestCase;

/**
 * @author vistart <i@vistart.me>
 */
class NoProfileTest extends TestCase
{
    protected $migrations = [
        \rhosocial\user\models\migrations\M170304140437CreateUserTable::class,
    ];
    /**
     * @var User 
     */
    protected $user;
    
    protected function setUp() : void {
        parent::setUp();
        $this->applyMigrations($this->migrations);
        $this->user = new User(['password' => '123456']);
    }
    
    protected function tearDown() : void {
        if ($this->user instanceof User) {
            try {
                $this->user->deregister();
            } catch (\Exception $ex) {

            }
            $this->user = null;
        }
        User::deleteAll();
        $this->revertMigrations($this->migrations);
        parent::tearDown();
    }
    
    public function testNew()
    {
        $this->assertTrue($this->user->register());
        $this->assertFalse($this->user->profileClass);
        $this->assertNull($this->user->getProfile());
        $this->assertNull($this->user->profile);
    }
}