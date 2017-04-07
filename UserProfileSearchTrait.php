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

namespace rhosocial\user;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
trait UserProfileSearchTrait
{
    public $createdFrom;
    protected $createdFromInUtc;
    public $createdTo;
    protected $createdToInUtc;
    /**
     * 
     * @return type
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['nickname', 'string'],
            [['createdFrom', 'createdTo'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm'],
            [['createdFrom', 'createdTo'], 'gmdate'],
        ];
    }

    public function gmdate($attribute, $params, $validator)
    {
        if (isset($this->$attribute)) {
            $timestamp = strtotime($this->$attribute);
            $this->{$attribute . 'InUtc'} = gmdate('Y-m-d H:i:s', $timestamp);
        }
    }

    /**
     * 
     * @return type
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * 
     * @param type $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = static::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageParam' => 'user-page',
                'defaultPageSize' => 20,
                'pageSizeParam' => 'user-per-page',
            ],
            'sort' => [
                'sortParam' => 'user-sort',
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query = $query->andFilterWhere([
            'LIKE', 'id', $this->id,
        ])->andFilterWhere([
            'LIKE', 'nickname', $this->nickname,
        ])->andFilterWhere([
            '>=', 'created_at', $this->createdFromInUtc,
        ])->andFilterWhere([
            '<=', 'created_at', $this->createdToInUtc,
        ]);
        $dataProvider->query = $query;
        return $dataProvider;
    }

    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['createdFrom'] = Yii::t('user', 'From');
        $attributeLabels['createdTo'] = Yii::t('user', 'To');
        return $attributeLabels;
    }
}
