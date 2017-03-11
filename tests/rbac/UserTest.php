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
use rhosocial\user\rbac\permissions\CreateAdminUser;
use rhosocial\user\rbac\permissions\CreateUser;
use rhosocial\user\rbac\permissions\DeleteAdminUser;
use rhosocial\user\rbac\permissions\DeleteMyself;
use rhosocial\user\rbac\permissions\DeleteUser;
use rhosocial\user\rbac\permissions\UpdateAdminUser;
use rhosocial\user\rbac\permissions\UpdateMyself;
use rhosocial\user\rbac\permissions\UpdateUser;
use rhosocial\user\rbac\roles\Admin as AdminRole;
use rhosocial\user\rbac\roles\User as UserRole;
use Yii;
use yii\rbac\Role;

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
    
    /**
     * @group user
     * @group rbac
     */
    public function testUserCreateUser()
    {
        $role = new UserRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertTrue(Yii::$app->user->login($this->user));
        $this->assertInstanceOf(Assignment::class, Yii::$app->authManager->getAssignment($role->name, $this->user));
        
        $this->assertFalse(\Yii::$app->user->can((new CreateAdminUser())->name));
        $this->assertFalse(\Yii::$app->user->can((new CreateUser())->name));
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testAdminCreateUser()
    {
        $role = new AdminRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertTrue(Yii::$app->user->login($this->user));
        $this->assertInstanceOf(Assignment::class, Yii::$app->authManager->getAssignment($role->name, $this->user));
        
        $this->assertFalse(\Yii::$app->user->can((new CreateAdminUser())->name));
        $this->assertTrue(\Yii::$app->user->can((new CreateUser())->name));
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testUserUpdateUser()
    {
        $role = new UserRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertTrue(Yii::$app->user->login($this->user));
        $this->assertInstanceOf(Assignment::class, Yii::$app->authManager->getAssignment($role->name, $this->user));
        
        $this->assertFalse(\Yii::$app->user->can((new UpdateAdminUser())->name));
        $this->assertTrue(\Yii::$app->user->can((new UpdateMyself())->name));
        $this->assertFalse(\Yii::$app->user->can((new UpdateUser())->name));
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testAdminUpdateUser()
    {
        $role = new AdminRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertTrue(Yii::$app->user->login($this->user));
        $this->assertInstanceOf(Assignment::class, Yii::$app->authManager->getAssignment($role->name, $this->user));
        
        $this->assertFalse(\Yii::$app->user->can((new UpdateAdminUser())->name));
        $this->assertTrue(\Yii::$app->user->can((new UpdateMyself())->name));
        $this->assertTrue(\Yii::$app->user->can((new UpdateUser())->name));
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testUserDeleteUser()
    {
        $role = new UserRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertTrue(Yii::$app->user->login($this->user));
        $this->assertInstanceOf(Assignment::class, Yii::$app->authManager->getAssignment($role->name, $this->user));
        
        $this->assertFalse(\Yii::$app->user->can((new DeleteAdminUser())->name));
        $this->assertTrue(\Yii::$app->user->can((new DeleteMyself())->name));
        $this->assertFalse(\Yii::$app->user->can((new DeleteUser())->name));
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testAdminDeleteUser()
    {
        $role = new AdminRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertTrue(Yii::$app->user->login($this->user));
        $this->assertInstanceOf(Assignment::class, Yii::$app->authManager->getAssignment($role->name, $this->user));
        
        $this->assertFalse(\Yii::$app->user->can((new DeleteAdminUser())->name));
        $this->assertTrue(\Yii::$app->user->can((new DeleteMyself())->name));
        $this->assertTrue(\Yii::$app->user->can((new DeleteUser())->name));
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testGetRolesByUser()
    {
        $role = new UserRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertTrue(Yii::$app->user->login($this->user));
        $roles = Yii::$app->authManager->getRolesByUser($this->user);
        $this->assertCount(1, $roles);
        $this->assertInstanceOf(Role::class, $roles[$role->name]);
    }
}