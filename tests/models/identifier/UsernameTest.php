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

namespace rhosocial\user\tests\models\identifier;

use rhosocial\base\helpers\Number;
use rhosocial\user\models\migrations\M170304140437CreateUserTable;
use rhosocial\user\models\migrations\M170307150614CreatePasswordHistoryTable;
use rhosocial\user\tests\data\models\identifier\Username;
use rhosocial\user\tests\data\models\user\User;
use rhosocial\user\tests\TestCase;
use yii\helpers\Inflector;

/**
 * Class UsernameTest
 * @package rhosocial\user\tests\models
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UsernameTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $username = 'vistart';

    protected $migrations = [
        \rhosocial\user\models\migrations\M170304140437CreateUserTable::class,
        \rhosocial\user\models\migrations\M170307150614CreatePasswordHistoryTable::class,
        \rhosocial\user\models\identifier\migrations\m170507_131103_createUsernameTable::class,
    ];

    /**
     *
     */
    protected function setUp() : void
    {
        parent::setUp();
        $this->applyMigrations($this->migrations);
        $this->user = new User(['password' => '123456']);
        $result = $this->user->register([$this->user->createUsername($this->username)]);
        if (!is_bool($result)) {
            echo $result->getMessage();
            $this->fail();
        }
        $this->assertTrue($result);
        $this->assertTrue(is_numeric($this->user->getID()));
    }

    protected function tearDown() : void
    {
        User::deleteAll();
        $this->revertMigrations($this->migrations);
        parent::tearDown();
    }

    /**
     * @group username
     */
    public function testNormal()
    {
        $this->assertEquals($this->username, $this->user->username->content);
        $this->assertEquals($this->username, (string)$this->user->username);
    }

    /**
     * @group username
     */
    public function testSet()
    {
        $this->user->username = $this->username . '1';
        $this->assertEquals($this->username . '1', (string)$this->user->getUsername()->one());

        $username = new Username(["content" => $this->username . '2']);
        $this->user->username = $username;
        $this->assertEquals($this->username . '2', (string)$this->user->getUsername()->one());
    }

    /**
     * @group username
     */
    public function testRemove()
    {
        $this->assertTrue($this->user->removeUsername());
        $this->assertNull($this->user->getUsername()->one());
        $this->assertTrue($this->user->hasEnabledUsername());
    }

    /**
     * @group username
     */
    public function testUnique()
    {
        $this->assertEquals($this->username, (string)$this->user->username);
        $user = new User(['password' => '123456']);
        try {
            $result = $user->register([$user->createUsername($this->username)]);
            if ($result instanceof \Exception) {
                throw $result;
            }
            $this->fail('Registration should be failed.');
        } catch (\Exception $ex) {
            $this->assertNotEmpty($ex->getMessage());
        }
        $user = new User(['password' => '123456']);
        $this->assertTrue($user->register([$user->createUsername($this->username . '1')]));
    }

    /**
     * @group username
     */
    public function testDisableUsername() {
        $user = new User(['password' => '123456']);
        $user->usernameClass = false;
        $this->assertFalse($user->hasEnabledUsername());
        $this->assertNull($user->getUsername());
        $this->assertNull($user->createUsername());
    }

    /**
     * @group username
     */
    public function testNullUsername() {
        $user = new User(['password' => '123456']);
        $this->assertTrue($user->register());
        $username = $user->createUsername();
        $this->assertInstanceOf(Username::class, $username);
        $this->assertFalse($username->save());
        $message = Inflector::camelize($username->contentAttribute) . " cannot be blank.";
        $this->assertEquals($message, $username->getErrorSummary(false)[0]);
    }

    /**
     * @group username
     */
    public function testStringifyUsername() {
        $user = new User(['password' => '123456']);
        $random = Number::randomNumber();
        $username = $user->createUsername($random);
        $this->assertEquals($random, (string)$username);
    }
}
