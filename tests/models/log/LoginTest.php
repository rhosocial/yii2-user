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

use rhosocial\user\tests\data\models\log\Login;
use rhosocial\user\tests\data\models\log\LoginDeleteExtra;
use rhosocial\user\tests\data\models\log\LoginDeleteExpired;
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
        $this->assertInstanceOf($this->user->loginLogClass, $latest);
    }
    
    /**
     * @group user
     * @group log
     * @group login
     */
    public function testLimit()
    {
        $login = new \rhosocial\user\models\log\Login(['limitMax' => 1, 'limitDuration' => 86399]);
        $this->assertEquals(100, $login->limitMax);
        $this->assertEquals(90 * 86400, $login->limitDuration);
        
        $login = new \rhosocial\user\models\log\Login(['limitMax' => 2, 'limitDuration' => 86400]);
        $this->assertEquals(2, $login->limitMax);
        $this->assertEquals(86400, $login->limitDuration);
        
        $login = new \rhosocial\user\models\log\Login(['limitMax' => 100, 'limitDuration' => 89 * 86400]);
        $this->assertEquals(100, $login->limitMax);
        $this->assertEquals(89 * 86400, $login->limitDuration);
    }
    
    /**
     * @group user
     * @group log
     * @group login
     */
    public function testDeleteExtraLogWithInvalidLimit()
    {
        $login = new Login();
        $login->limitMax = $login;
        $login->limitDuration = $login;
        $login->onDeleteExtraRecords(new \yii\base\Event(['sender' => $login]));
    }
    
    /**
     * @group user
     * @group log
     * @group login
     */
    public function testStatus()
    {
        $login = new Login();
        $this->assertNull($login->getStatusDesc());
        $login->status = 0x001;
        $this->assertEquals(Login::$statuses[$login->status], $login->getStatusDesc());
    }
    
    /**
     * @group user
     * @group log
     * @group login
     */
    public function testDevice()
    {
        $login = new Login();
        $this->assertNull($login->getDeviceDesc());
        $login->device = 0x011;
        $this->assertEquals(Login::$devices[$login->device], $login->getDeviceDesc());
    }
    
    /**
     * @group user
     * @group log
     * @group login
     */
    public function testDeleteExtraRecords()
    {
        $this->user->loginLogClass = LoginDeleteExtra::class;
        $this->assertCount(0, $this->user->loginLogs);
        $this->assertNull($this->user->latestLoginLog);
        
        $this->assertTrue(Yii::$app->user->login($this->user, 24 * 86400));
        $this->assertCount(1, $this->user->loginLogs);
        $this->assertInstanceOf($this->user->loginLogClass, $this->user->latestLoginLog);
        $this->assertTrue(Yii::$app->user->logout());
        
        $this->assertTrue(Yii::$app->user->login($this->user, 24 * 86400));
        $this->assertCount(2, $this->user->loginLogs);
        $this->assertInstanceOf($this->user->loginLogClass, $this->user->latestLoginLog);
        $this->assertTrue(Yii::$app->user->logout());
        
        $this->assertTrue(Yii::$app->user->login($this->user, 24 * 86400));
        $this->assertCount(2, $this->user->loginLogs);
        $this->assertInstanceOf($this->user->loginLogClass, $this->user->latestLoginLog);
        $this->assertTrue(Yii::$app->user->logout());
    }
    
    /**
     * @group user
     * @group log
     * @group login
     */
    public function testDeleteExpiredRecords()
    {
        $this->user->loginLogClass = LoginDeleteExpired::class;
        $this->assertCount(0, $this->user->loginLogs);
        $this->assertNull($this->user->latestLoginLog);
        
        $this->assertTrue(Yii::$app->user->login($this->user, 24 * 86400));
        $this->assertTrue(Yii::$app->user->logout());
        $this->assertTrue(Yii::$app->user->login($this->user, 24 * 86400));
        $this->assertTrue(Yii::$app->user->logout());
        
        $this->assertCount(2, $this->user->loginLogs);
        $this->assertInstanceOf($this->user->loginLogClass, $this->user->latestLoginLog);
        sleep(2);
        
        $this->assertTrue(Yii::$app->user->login($this->user, 24 * 86400));
        $this->assertTrue(Yii::$app->user->logout());
        $this->assertTrue(Yii::$app->user->login($this->user, 24 * 86400));
        $this->assertTrue(Yii::$app->user->logout());
        sleep(2);
        $this->assertCount(2, $this->user->loginLogs);
        $this->assertInstanceOf($this->user->loginLogClass, $this->user->latestLoginLog);
    }
}
