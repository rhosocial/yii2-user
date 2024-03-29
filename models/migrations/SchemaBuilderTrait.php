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
namespace rhosocial\user\models\migrations;

use rhosocial\user\models\migrations\mysql\Schema as MySQLSchema;
use yii\db\Schema;
use yii\db\Connection;
use yii\db\ColumnSchemaBuilder;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
trait SchemaBuilderTrait
{
    /**
     * @return Connection the database connection to be used for schema building.
     */
    protected abstract function getDb();
    
    /**
     * 
     * @return ColumnSchemaBuilder
     */
    public function blob()
    {
        if ($this->getDb()->driverName == 'mysql') {
            return (new MySQLSchema(['db' => $this->getDb()]))->createColumnSchemaBuilder(MySQLSchema::TYPE_BLOB);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function binary($length = null)
    {
        return (new MySQLSchema(['db' => $this->getDb()]))->createColumnSchemaBuilder(Schema::TYPE_BINARY, $length);
    }
    
    public function varbinary($length = null)
    {
        if ($this->getDb()->driverName == 'mysql') {
            return (new MySQLSchema(['db' => $this->getDb()]))->createColumnSchemaBuilder(MySQLSchema::TYPE_VARBINARY, $length);
        }
    }
    
    public function varchar($length = null)
    {
        if ($this->getDb()->driverName == 'mysql') {
            return (new MySQLSchema(['db' => $this->getDb()]))->createColumnSchemaBuilder(MySQLSchema::TYPE_VARCHAR, $length);
        }
    }
    
    public function binaryPk()
    {
        if ($this->getDb()->driverName == 'mysql') {
            return (new MySQLSchema(['db' => $this->getDb()]))->createColumnSchemaBuilder(MySQLSchema::TYPE_BINARY_PK);
        }
    }
    
    public function tinyInteger($length = null)
    {
        if ($this->getDb()->driverName == 'mysql') {
            return (new MySQLSchema(['db' => $this->getDb()]))->createColumnSchemaBuilder(MySQLSchema::TYPE_TINYINT, $length);
        }
    }

    public function integerPk($length = null)
    {
        if ($this->getDb()->driverName == 'mysql') {
            return (new MySQLSchema(['db' => $this->getDb()]))->createColumnSchemaBuilder(MySQLSchema::TYPE_PK, $length);
        }
    }

    public function bigIntegerPk($length = null)
    {
        if ($this->getDb()->driverName == 'mysql') {
            return (new MySQLSchema(['db' => $this->getDb()]))->createColumnSchemaBuilder(MySQLSchema::TYPE_BIGPK, $length);
        }
    }

    public function unsignedIntegerPk($length = null)
    {
        if ($this->getDb()->driverName == 'mysql') {
            return (new MySQLSchema(['db' => $this->getDb()]))->createColumnSchemaBuilder(MySQLSchema::TYPE_UPK, $length);
        }
    }

    public function unsignedBigIntegerPk($length = null)
    {
        if ($this->getDb()->driverName == 'mysql') {
            return (new MySQLSchema(['db' => $this->getDb()]))->createColumnSchemaBuilder(MySQLSchema::TYPE_UBIGPK, $length);
        }
    }
}