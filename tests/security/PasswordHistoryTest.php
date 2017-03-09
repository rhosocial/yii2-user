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
use rhosocial\user\tests\data\User;

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
    
    protected function setUp() {
        parent::setUp();
        $this->user = new User(['password' => $this->password1]);
    }
    
    protected function tearDown() {
        if ($this->user instanceof User && !$this->user->isNewRecord) {
            $this->user->deregister();
        }
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
        
        $this->assertTrue($this->user->applyForNewPassword());
        $this->assertTrue($this->user->resetPassword($this->password2, $this->user->{$this->user->passwordResetTokenAttribute}));
        $this->assertTrue($this->user->validatePassword($this->password2));
        $this->assertCount(2, $this->user->getPasswordHistories());
        
        $this->assertTrue($this->user->getPasswordHistories()[0]->validatePassword($this->password2));
    }
    
    /**
     * @group password
     */
    public function testAllowDuplicatePassword()
    {
        $this->user->allowDuplicatePassword = true;
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
    public function testDisallowDuplicatePassword()
    {
        $this->user->allowDuplicatePassword = false;
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
}