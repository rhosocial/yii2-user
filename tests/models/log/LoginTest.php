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

namespace rhosocial\user\tests\models\log;

use rhosocial\user\models\log\Login;
use rhosocial\user\tests\data\User;
use rhosocial\user\tests\TestCase;
use Yii;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class LoginTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;
    
    protected $password1 = '123456';
    
    protected function setUp() {
        parent::setUp();
        $this->user = new User(['password' => $this->password1]);
        $this->assertTrue($this->user->register());
    }
    
    protected function tearDown() {
        if ($this->user instanceof User) {
            try {
                $this->user->deregister();
            } catch (\Exception $ex) {

            }
            $this->user =  null;
        }
        User::deleteAll();
        parent::tearDown();
    }
    
    /**
     * @group user
     * @group log
     * @group login
     */
    public function testLogin()
    {
        $this->assertTrue(Yii::$app->user->login($this->user, 24 * 86400));
        $loginLogs = $this->user->loginLogs;
        $this->assertCount(1, $loginLogs);
    }
    
    /**
     * @group user
     * @group log
     * @group login
     */
    public function testLatestLogin()
    {
        $this->assertTrue(Yii::$app->user->login($this->user, 24 * 86400));
        $latest = $this->user->latestLoginLog;
        $this->assertInstanceOf(Login::class, $latest);
    }
}