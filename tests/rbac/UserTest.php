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

namespace rhosocial\user\tests\rbac;

use rhosocial\user\tests\data\User;
use rhosocial\user\tests\data\Profile;
use rhosocial\user\tests\TestCase;
use rhosocial\user\rbac\Assignment;
use rhosocial\user\rbac\roles\User as UserRole;
use Yii;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UserTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;
    
    /**
     * @var Profile
     */
    protected $profile;
    
    protected $password1 = '123456';
    
    protected function setUp() {
        parent::setUp();
        $this->user = new User(['password' => $this->password1]);
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
     * @group user
     * @group rbac
     */
    public function testAssignWhenRegistering()
    {
        $role = new UserRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $assignment = Yii::$app->authManager->getAssignment($role->name, $this->user->getGUID());
        $this->assertInstanceOf(Assignment::class, $assignment);
    }
}