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

namespace rhosocial\user\tests;

use yii\di\Container;
use yii\helpers\ArrayHelper;
use Yii;
use yii\db\Connection;

/**
 * Description of TestCase
 *
 * @author vistart <i@vistart.me>
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase {

    public static $params;

    /**
     * Returns a test configuration param from /data/config.php
     * @param  string $name params name
     * @param  mixed $default default value to use when param is not set.
     * @return mixed  the value of the configuration param
     */
    public static function getParam($name, $default = null) {
        if (static::$params === null) {
            static::$params = require(__DIR__ . '/data/config.php');
        }

        return isset(static::$params[$name]) ? static::$params[$name] : $default;
    }

    /**
     * Clean up after test.
     * By default the application created with [[mockApplication]] will be destroyed.
     */
    protected function tearDown() : void {
        $migrations = self::getParam('migrations');
        $this->revertMigrations($migrations);
        parent::tearDown();
        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication($config = [], $appClass = '\yii\console\Application') {
        new $appClass(ArrayHelper::merge([
                    'id' => 'testapp',
                    'basePath' => __DIR__,
                    'vendorPath' => dirname(__DIR__) . '/vendor',
                    'timeZone' => 'Asia/Shanghai',
                        ], $config));
    }

    protected function mockWebApplication($config = [], $appClass = '\yii\web\Application') {
        new $appClass(ArrayHelper::merge([
                    'id' => 'testapp',
                    'basePath' => __DIR__,
                    'vendorPath' => dirname(__DIR__) . '/vendor',
                    'timeZone' => 'Asia/Shanghai',
                    'aliases' => [
                        '@bower' => '@vendor/bower-asset',
                        '@npm' => '@vendor/npm-asset',
                    ],
                    'components' => [
                        'i18n' => [
                            'translations' => [
                                'user*' => [
                                    'class' => 'yii\i18n\PhpMessageSource',
                                    'basePath' => dirname(__DIR__) . '/messages',
                                    'sourceLanguage' => 'en-US',
                                    'fileMap' => [
                                        'user' => 'user.php',
                                    ],
                                ],
                            ],
                        ],
                        'request' => [
                            'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                            'scriptFile' => __DIR__ . '/index.php',
                            'scriptUrl' => '/index.php',
                            'isConsoleRequest' => false,
                        ],
                        'user' => [
                            'class' => \rhosocial\user\components\User::class,
                            'identityClass' => \rhosocial\user\tests\data\models\user\User::class,
                            'enableAutoLogin' => true,
                        ],
                        'authManager' => [
                            'class' => \rhosocial\user\rbac\DbManager::class,
                        ],
                    ]
            ], $config));
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication() {
        Yii::$app = null;
        Yii::$container = new Container();
    }

    /**
     * @param array|null $migrations
     * @return void
     */
    protected function applyMigrations(array|null $migrations) : void {
        if (empty($migrations)) {
            return;
        }
        foreach ($migrations as $key => $migrationClass) {
            $migration = new $migrationClass(['compact' => true]);
            /* @var $migration \yii\db\Migration */
            $migration->up();
        }
    }

    /**
     * @param array|null $migrations
     * @return void
     */
    protected function revertMigrations(array|null $migrations) : void {
        if (empty($migrations)) {
            return;
        }
        foreach (array_reverse($migrations) as $key => $migrationClass) {
            $migration = new $migrationClass(['compact' => true]);
            /* @var $migration \yii\db\Migration */
            $migration->down();
        }
    }

    protected function setUp() : void {
        $databases = self::getParam('databases');
        $params = isset($databases['mysql']) ? $databases['mysql'] : null;
        if ($params === null) {
            $this->markTestSkipped('No mysql server connection configured.');
        }
        if (array_key_exists('class', $params)) {
            unset($params['class']);
        }
        $connection = new Connection($params);
        $cacheParams = self::getParam('cache');
        /*
        if ($cacheParams === null) {
            $this->markTestSkipped('No cache component configured.');;
        }*/
        $this->mockWebApplication(['components' => ['db' => $connection, 'cache' => $cacheParams]]);

        $migrations = self::getParam('migrations');
        $this->applyMigrations($migrations);

        parent::setUp();
    }

    /**
     * @param  boolean    $reset whether to clean up the test database
     * @return Connection
     */
    public function getConnection($reset = true) {
        $databases = self::getParam('databases');
        $params = isset($databases['mysql']) ? $databases['mysql'] : [];
        $db = new Connection($params);
        if ($reset) {
            $db->open();
            //$db->flushdb();
        }

        return $db;
    }

}
