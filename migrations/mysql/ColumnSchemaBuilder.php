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
class ColumnSchemaBuilder extends \yii\db\mysql\ColumnSchemaBuilder
{
    public $collate;
    
    public $charset;
    
    /**
     * @inheritdoc
     */
    public $categoryMap = [
        Schema::TYPE_PK => self::CATEGORY_PK,
        Schema::TYPE_UPK => self::CATEGORY_PK,
        Schema::TYPE_BIGPK => self::CATEGORY_PK,
        Schema::TYPE_UBIGPK => self::CATEGORY_PK,
        Schema::TYPE_CHAR => self::CATEGORY_STRING,
        Schema::TYPE_VARCHAR => self::CATEGORY_STRING,
        Schema::TYPE_STRING => self::CATEGORY_STRING,
        Schema::TYPE_TEXT => self::CATEGORY_STRING,
        Schema::TYPE_SMALLINT => self::CATEGORY_NUMERIC,
        Schema::TYPE_INTEGER => self::CATEGORY_NUMERIC,
        Schema::TYPE_BIGINT => self::CATEGORY_NUMERIC,
        Schema::TYPE_FLOAT => self::CATEGORY_NUMERIC,
        Schema::TYPE_DOUBLE => self::CATEGORY_NUMERIC,
        Schema::TYPE_DECIMAL => self::CATEGORY_NUMERIC,
        Schema::TYPE_DATETIME => self::CATEGORY_TIME,
        Schema::TYPE_TIMESTAMP => self::CATEGORY_TIME,
        Schema::TYPE_TIME => self::CATEGORY_TIME,
        Schema::TYPE_DATE => self::CATEGORY_TIME,
        Schema::TYPE_BINARY => self::CATEGORY_OTHER,
        Schema::TYPE_BINARY_PK => self::CATEGORY_PK,
        Schema::TYPE_BOOLEAN => self::CATEGORY_NUMERIC,
        Schema::TYPE_MONEY => self::CATEGORY_NUMERIC,
    ];

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        switch ($this->getTypeCategory()) {
            case self::CATEGORY_PK:
                $format = '{type}{length}{check}{comment}{append}{pos}';
                break;
            case self::CATEGORY_NUMERIC:
                $format = '{type}{length}{unsigned}{notnull}{unique}{default}{check}{comment}{append}{pos}';
                break;
            default:
                $format = '{type}{length}{notnull}{charset}{collate}{unique}{default}{check}{comment}{append}{pos}';
        }
        return $this->buildCompleteString($format);
    }
    
    protected function buildCharsetString()
    {
        return $this->charset ? " CHARACTER SET $this->charset" : "";
    }
    
    protected function buildCollateString()
    {
        return $this->collate ? " COLLATE $this->collate" : "";
    }

    /**
     * Returns the complete column definition from input format
     * @param string $format the format of the definition.
     * @return string a string containing the complete column definition.
     * @since 2.0.8
     */
    protected function buildCompleteString($format)
    {
        $placeholderValues = [
            '{type}' => $this->type,
            '{length}' => $this->buildLengthString(),
            '{unsigned}' => $this->buildUnsignedString(),
            '{notnull}' => $this->buildNotNullString(),
            '{charset}' => $this->buildCharsetString(),
            '{collate}' => $this->buildCollateString(),
            '{unique}' => $this->buildUniqueString(),
            '{default}' => $this->buildDefaultString(),
            '{check}' => $this->buildCheckString(),
            '{comment}' => $this->buildCommentString(),
            '{pos}' => $this->isFirst ? $this->buildFirstString() : $this->buildAfterString(),
            '{append}' => $this->buildAppendString(),
        ];
        return strtr($format, $placeholderValues);
    }
    
    /**
     * Add `collate` constraint.
     * @param string $collate
     * @return $this
     */
    public function collate($collate)
    {
        $this->collate = $collate;
        return $this;
    }
    
    /**
     * Add `character set` constraint.
     * @param string $charset
     * @return $this
     */
    public function charset($charset)
    {
        $this->charset = $charset;
        return $this;
    }
}