<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2022 vistart
 * @license https://vistart.me/license/
 */

namespace rhosocial\user\tests\rbac;

use rhosocial\user\tests\data\models\user\User;
use rhosocial\user\tests\data\models\user\Profile;
use rhosocial\user\tests\TestCase;
use rhosocial\user\rbac\Assignment;
use rhosocial\user\rbac\permissions\GrantAdmin;
use rhosocial\user\rbac\permissions\CreateUser;
use rhosocial\user\rbac\permissions\RevokeAdmin;
use rhosocial\user\rbac\permissions\DeleteMyself;
use rhosocial\user\rbac\permissions\DeleteUser;
use rhosocial\user\rbac\permissions\UpdateAdmin;
use rhosocial\user\rbac\permissions\UpdateMyself;
use rhosocial\user\rbac\permissions\UpdateUser;
use rhosocial\user\rbac\Role;
use rhosocial\user\rbac\roles\Admin as AdminRole;
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

    protected $migrations = [
        \rhosocial\user\models\migrations\M170304140437CreateUserTable::class,
        \rhosocial\user\models\migrations\M170304142349CreateProfileTable::class,
        \rhosocial\user\models\migrations\M170307150614CreatePasswordHistoryTable::class,
        \rhosocial\user\models\log\migrations\m170313_071528_createLoginLogTable::class,
        \rhosocial\user\rbac\migrations\M170310150337CreateAuthTables::class,
    ];
    
    protected function setUp() : void {
        parent::setUp();
        $this->applyMigrations($this->migrations);
        $this->user = new User(['password' => $this->password1]);
        $this->profile = $this->user->createProfile(['nickname' => 'vistart']);
    }
    
    protected function tearDown() : void
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
        $this->revertMigrations($this->migrations);
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
        $this->assertEquals($this->user->guid, User::findIdentityByGuid($this->user)->guid);
        $this->assertTrue(Yii::$app->user->login($this->user));
        $this->assertInstanceOf(Assignment::class, Yii::$app->authManager->getAssignment($role->name, $this->user));
        
        $this->assertFalse(\Yii::$app->user->can((new GrantAdmin())->name));
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
        
        $this->assertFalse(\Yii::$app->user->can((new GrantAdmin())->name));
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
        
        $this->assertFalse(\Yii::$app->user->can((new UpdateAdmin())->name));
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
        
        $this->assertFalse(\Yii::$app->user->can((new UpdateAdmin())->name));
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
        
        $this->assertFalse(\Yii::$app->user->can((new RevokeAdmin())->name));
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
        
        $this->assertFalse(\Yii::$app->user->can((new RevokeAdmin())->name));
        $this->assertTrue(\Yii::$app->user->can((new DeleteMyself())->name));
        $this->assertTrue(\Yii::$app->user->can((new DeleteUser())->name));
    }
    
    /**
     * Best Practise: Get All Assignments of user.
     * @group user
     * @group rbac
     */
    public function testGetRolesByUser()
    {
        $this->assertEquals([], Yii::$app->authManager->getRolesByUser(''));
        
        $role = new UserRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertTrue(Yii::$app->user->login($this->user));
        $roles = Yii::$app->authManager->getRolesByUser($this->user);
        $this->assertCount(1, $roles);
        $this->assertInstanceOf(Role::class, $roles[$role->name]);
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testGetAssignments()
    {
        $this->assertNull(Yii::$app->authManager->getAssignment(null, null));
        $this->assertTrue($this->user->register([$this->profile]));
        $this->assertNull(Yii::$app->authManager->getAssignment('', $this->user));
        
        $this->assertEquals([], Yii::$app->authManager->getAssignments(null));
        $this->assertNotNull(Yii::$app->authManager->getAssignments($this->user));
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testRevoke()
    {
        $role = new UserRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertFalse(Yii::$app->authManager->revoke($role, ''));
        $this->assertTrue(Yii::$app->authManager->revoke($role, $this->user));
        $this->assertFalse(Yii::$app->authManager->revoke($role, $this->user));
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testRevokeAll()
    {
        $this->assertFalse(Yii::$app->authManager->revokeAll(''));
        $role = new UserRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertTrue(Yii::$app->authManager->revokeAll($this->user));
        $this->assertFalse(Yii::$app->authManager->revokeAll($this->user));
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testGetUserGuidsByRoles()
    {
        $this->assertEquals([], Yii::$app->authManager->getUserGuidsByRole(''));
        $role = new UserRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $this->assertEquals($this->user->getGUID(), Yii::$app->authManager->getUserGuidsByRole($role->name)[0]);
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testGetPermissionsByUser()
    {
        $role = new UserRole();
        $this->assertTrue($this->user->register([$this->profile], $role));
        $permissions = Yii::$app->authManager->getPermissionsByUser($this->user);
        $this->assertArrayHasKey((new UpdateMyself)->name, $permissions);
        $this->assertArrayHasKey((new DeleteMyself)->name, $permissions);
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testFailedPermission()
    {
        $permission = new GrantAdmin();
        $this->assertTrue($this->user->register([$this->profile]));
        $this->assertEmpty(Yii::$app->authManager->getPermissionsByUser($this->user));
        $date = strtotime(gmdate('Y-m-d H:i:s'));
        $assignment = Yii::$app->authManager->assign($permission, $this->user, $date);
        /* @var $assignment Assignment */
        $this->assertInstanceOf(Assignment::class, $assignment);
        $this->assertEquals($date, $assignment->failedAt);
        sleep(1);
        $this->assertTrue($assignment->failedAt < strtotime(gmdate('Y-m-d H:i:s')));
        
        $this->assertNull(Yii::$app->authManager->getAssignment($permission->name, $this->user));
    }
    
    /**
     * @group user
     * @group rbac
     */
    public function testFailedRole()
    {
        $role = new AdminRole();
        $this->assertTrue($this->user->register([$this->profile]));
        $this->assertEmpty(Yii::$app->authManager->getPermissionsByUser($this->user));
        $date = strtotime(gmdate('Y-m-d H:i:s'));
        $assignment = Yii::$app->authManager->assign($role, $this->user, $date);
        /* @var $assignment Assignment */
        $this->assertInstanceOf(Assignment::class, $assignment);
        $this->assertEquals($date, $assignment->failedAt);
        sleep(1);
        $this->assertTrue($assignment->failedAt < strtotime(gmdate('Y-m-d H:i:s')));
        
        $this->assertNull(Yii::$app->authManager->getAssignment($role->name, $this->user));
    }
}