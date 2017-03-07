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

namespace rhosocial\user\migrations\mysql;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class Schema extends \yii\db\mysql\Schema
{
    const TYPE_BLOB = 'blob';
    const TYPE_BINARY_PK = 'binary_pk';
    const TYPE_GUID_PK = 'binary_pk';
    const TYPE_TINYINT = 'tinyint';
    const TYPE_VARCHAR = 'varchar';
    const TYPE_VARBINARY = 'varbinary';
    
    /**
     * @var array mapping from physical column types (keys) to abstract column types (values)
     */
    public $typeMap = [
        'tinyint' => self::TYPE_TINYINT,
        'bit' => self::TYPE_INTEGER,
        'smallint' => self::TYPE_SMALLINT,
        'mediumint' => self::TYPE_INTEGER,
        'int' => self::TYPE_INTEGER,
        'integer' => self::TYPE_INTEGER,
        'bigint' => self::TYPE_BIGINT,
        'float' => self::TYPE_FLOAT,
        'double' => self::TYPE_DOUBLE,
        'real' => self::TYPE_FLOAT,
        'decimal' => self::TYPE_DECIMAL,
        'numeric' => self::TYPE_DECIMAL,
        'tinytext' => self::TYPE_TEXT,
        'mediumtext' => self::TYPE_TEXT,
        'longtext' => self::TYPE_TEXT,
        'longblob' => self::TYPE_BLOB,
        'blob' => self::TYPE_BLOB,
        'text' => self::TYPE_TEXT,
        'string' => self::TYPE_STRING,
        'char' => self::TYPE_CHAR,
        'varchar' => self::TYPE_VARCHAR,
        'datetime' => self::TYPE_DATETIME,
        'year' => self::TYPE_DATE,
        'date' => self::TYPE_DATE,
        'time' => self::TYPE_TIME,
        'timestamp' => self::TYPE_TIMESTAMP,
        'enum' => self::TYPE_STRING,
        'binary' => self::TYPE_BINARY,
        'varbinary' => self::TYPE_VARBINARY,
    ];

    /**
     * Creates a query builder for the MySQL database.
     * @return QueryBuilder query builder instance
     */
    public function createQueryBuilder()
    {
        if ($this->db->driverName == 'mysql') {
            return new QueryBuilder($this->db);
        }
    }

    /**
     * @inheritdoc
     */
    public function createColumnSchemaBuilder($type, $length = null)
    {
        return new ColumnSchemaBuilder($type, $length, $this->db);
    }
}