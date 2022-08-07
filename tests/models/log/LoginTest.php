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

namespace rhosocial\user\tests\models\log;

use rhosocial\user\tests\data\models\log\Login;
use rhosocial\user\tests\data\models\log\LoginDeleteExtra;
use rhosocial\user\tests\data\models\log\LoginDeleteExpired;
use rhosocial\user\tests\data\models\user\User;
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

    protected $migrations = [
        \rhosocial\user\models\migrations\M170304140437CreateUserTable::class,
        \rhosocial\user\models\migrations\M170307150614CreatePasswordHistoryTable::class,
        \rhosocial\user\models\log\migrations\m170313_071528_createLoginLogTable::class,
    ];

    protected function setUp() : void {
        parent::setUp();
        $this->applyMigrations($this->migrations);
        $this->user = new User(['password' => $this->password1]);
        $result = $this->user->register();
        if (!is_bool($result)) {
            echo $result->getMessage();
            $this->fail();
        }
        $this->assertTrue($result);
        $this->assertTrue(is_numeric($this->user->getID()));
    }

    protected function tearDown() : void {
        if ($this->user instanceof User) {
            try {
                $this->user->deregister();
            } catch (\Exception $ex) {

            }
            $this->user =  null;
        }
        User::deleteAll();
        $this->revertMigrations($this->migrations);
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
