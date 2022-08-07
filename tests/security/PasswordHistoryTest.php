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

namespace rhosocial\user\tests\security;

use rhosocial\user\tests\TestCase;
use rhosocial\user\tests\data\models\user\User;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class PasswordHistoryTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;
    
    protected $password1 = '123456';
    
    protected $password2 = '654321';

    protected $migrations = [
        \rhosocial\user\models\migrations\M170304140437CreateUserTable::class,
        \rhosocial\user\models\migrations\M170307150614CreatePasswordHistoryTable::class,
    ];
    
    protected function setUp() : void {
        parent::setUp();
        $this->applyMigrations($this->migrations);
        $this->user = new User(['password' => $this->password1]);
    }
    
    protected function tearDown() : void {
        if ($this->user instanceof User && !$this->user->isNewRecord) {
            $this->user->deregister();
        }
        $this->revertMigrations($this->migrations);
        parent::tearDown();
    }
    
    /**
     * @group password
     */
    public function testNew()
    {
        $this->assertTrue($this->user->register());
        $ph = $this->user->passwordHistories;
        $this->assertCount(1, $ph);
        $this->assertTrue($ph[0]->validatePassword($this->password1));
        $this->assertTrue($this->user->deregister());
        $this->assertEmpty($this->user->passwordHistories);
    }
    
    /**
     * @group password
     */
    public function testResetPassword()
    {
        $this->assertTrue($this->user->register());
        $this->assertCount(1, $this->user->passwordHistories);
        sleep(1);
        $this->assertTrue($this->user->applyForNewPassword());
        $this->assertTrue($this->user->resetPassword($this->password2, $this->user->{$this->user->passwordResetTokenAttribute}));
        $this->assertTrue($this->user->validatePassword($this->password2));
        $this->assertCount(2, $this->user->getPasswordHistories());
        
        $this->assertTrue($this->user->getPasswordHistories()[0]->validatePassword($this->password2));
    }
    
    /**
     * @group password
     */
    public function testAllowUsedPassword()
    {
        $this->user->allowUsedPassword = true;
        $this->assertTrue($this->user->register());
        $this->assertTrue($this->user->validatePassword($this->password1));
        $this->assertCount(1, $this->user->getPasswordHistories());
        
        $this->assertTrue($this->user->applyForNewPassword());
        $this->assertTrue($this->user->resetPassword($this->password1, $this->user->{$this->user->passwordResetTokenAttribute}));
        $this->assertTrue($this->user->validatePassword($this->password1));
        
        $this->assertCount(2, $this->user->getPasswordHistories());
        
        foreach ($this->user->getPasswordHistories() as $history)
        {
            $history->validatePassword($this->password1);
        }
    }
    
    protected $resetPasswordFailed = false;
    
    /**
     * 
     * @param \yii\base\ModelEvent $event
     */
    public function onResetPasswordFailed($event)
    {
        $this->assertEquals(User::$eventResetPasswordFailed, $event->name);
        $this->resetPasswordFailed = !$this->resetPasswordFailed;
    }
    
    /**
     * @group password
     */
    public function testDisallowUsedPassword()
    {
        $this->user->allowUsedPassword = false;
        $this->user->on(User::$eventResetPasswordFailed, [$this, 'onResetPasswordFailed']);
        $this->assertTrue($this->user->register());
        $this->assertTrue($this->user->validatePassword($this->password1));
        $this->assertCount(1, $this->user->getPasswordHistories());
        
        $this->assertTrue($this->user->applyForNewPassword());
        $this->assertFalse($this->resetPasswordFailed);
        $this->assertFalse($this->user->resetPassword($this->password1, $this->user->{$this->user->passwordResetTokenAttribute}));
        $this->assertTrue($this->resetPasswordFailed);
        $this->assertTrue($this->user->validatePassword($this->password1));
    }
    
    /**
     * @group password
     */
    public function testNoPasswordHistory()
    {
        $this->user->passwordHistoryClass = false;
        $this->assertFalse($this->user->getPasswordHistories());
        $this->assertFalse($this->user->passwordHistories);
        
        $this->assertFalse($this->user->addPasswordHistory($this->password1));
        $this->assertFalse($this->user->addPasswordHashToHistory(\Yii::$app->security->generatePasswordHash($this->password1)));
    }
    
    /**
     * @group password
     */
    public function testInvalidUser()
    {
        $class = $this->user->passwordHistoryClass;
        $this->assertTrue($this->user->register());
        
        try {
            $class::isUsed($this->user->pass_hash, new User());
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertInstanceOf(\yii\base\InvalidParamException::class, $ex);
        }
        
        try {
            $class::passHashIsUsed(\Yii::$app->security->generatePasswordHash($this->password1), new User());
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertInstanceOf(\yii\base\InvalidParamException::class, $ex);
        }
        
        try {
            $class::addHash($this->user->pass_hash, new User());
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertInstanceOf(\yii\base\InvalidParamException::class, $ex);
        }
        
        try {
            $class::first(new User());
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertInstanceOf(\yii\base\InvalidParamException::class, $ex);
        }
        
        try {
            $class::last(new User());
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertInstanceOf(\yii\base\InvalidParamException::class, $ex);
        }
    }
    
    /**
     * @group password
     */
    public function testExistedPasswordHash()
    {
        $class = $this->user->passwordHistoryClass;
        $this->assertTrue($this->user->register());
        
        try {
            $class::addHash($this->user->pass_hash, $this->user);
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertInstanceOf(\yii\base\InvalidParamException::class, $ex);
        }
        
        $this->user->allowUsedPassword = false;
        
        try {
            $class::add($this->password1, $this->user);
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertInstanceOf(\yii\base\InvalidParamException::class, $ex);
        }
        
        try {
            $class::add($this->user->pass_hash, $this->user);
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertInstanceOf(\yii\base\InvalidParamException::class, $ex);
        }
    }
    
    /**
     * @group password
     */
    public function testFirstPassword()
    {
        $class = $this->user->passwordHistoryClass;
        $this->assertTrue($this->user->register());
        sleep(1);
        $this->assertTrue($this->user->addPasswordHistory($this->password2));
        $p = $class::first($this->user);
        $this->assertInstanceOf($class, $p);
        $this->assertEquals($this->user->getPasswordHistories()[1], $p);
    }
    
    /**
     * @group password
     */
    public function testLastPassword()
    {
        $class = $this->user->passwordHistoryClass;
        $this->assertTrue($this->user->register());
        sleep(1);
        $this->assertTrue($this->user->addPasswordHistory($this->password2));
        $p = $class::last($this->user);
        $this->assertInstanceOf($class, $p);
        $this->assertEquals($this->user->getPasswordHistories()[0], $p);
    }
    
    /**
     * @group password
     */
    public function testAddPasswordHash()
    {
        $class = $this->user->passwordHistoryClass;
        $this->assertTrue($this->user->register());
        
        $this->assertTrue($class::add(\Yii::$app->security->generatePasswordHash($this->password2), $this->user));
        $this->assertTrue($this->user->addPasswordHashToHistory(\Yii::$app->security->generatePasswordHash($this->password1 . $this->password2)));
    }
        
    /**
     * @group password
     */
    public function testOnAddPasswordToHistory()
    {
        $class = $this->user->passwordHistoryClass;
        $this->assertTrue($this->user->register());
        
        $event = new \yii\base\ModelEvent(['sender' => $this->user, 'data' => ['password' => $this->password2]]);
        $this->assertTrue($this->user->onAddPasswordToHistory($event));
        
        $event->data = ['pass' => $this->password2];
        $this->assertFalse($this->user->onAddPasswordToHistory($event));
    }
}