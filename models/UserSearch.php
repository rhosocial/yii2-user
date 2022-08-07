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

namespace rhosocial\user\models;

use rhosocial\base\models\queries\BaseUserQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class UserSearchTrait
 *
 * @package rhosocial\user
 * @version 1.0
 * @author vistart <i@vistart.me>
 */
class UserSearch extends Model
{
    public $userClass = User::class;
    public static function find()
    {
        $noInit = new static;
        $class = $noInit->userClass;
        if (empty($class)) {
            return null;
        }
        return $class::find();
    }
    public $userAlias = 'u_alias';
    public $profileAlias = 'p_alias';
    public $id;
    public $nickname;
    public $first_name;
    public $last_name;
    /**
     * @var string
     */
    public $createdFrom;
    protected $createdFromInUtc;

    /**
     * @var string
     */
    public $createdTo;
    protected $createdToInUtc;

    /**
     * @var string Gender filter.
     */
    public $gf;

    /**
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['nickname', 'first_name', 'last_name'], 'string'],
            [['createdFrom', 'createdTo'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm'],
            [['createdFrom', 'createdTo'], 'gmdate'],
            ['gf', 'in', 'range' => array_keys(Profile::getGenderDescsWithEmpty())],
            ['gf', 'default', 'value' => ''],
        ];
    }

    /**
     * Convert time attribute to UTC time.
     * @param string $attribute
     * @param array $params
     * @param mixed $validator
     */
    public function gmdate($attribute, $params, $validator)
    {
        if (isset($this->$attribute)) {
            $timestamp = strtotime($this->$attribute);
            $this->{$attribute . 'InUtc'} = gmdate('Y-m-d H:i:s', $timestamp);
        }
    }

    /**
     * Search
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = static::find();
        /* @var $query BaseUserQuery */
        $userClass = $this->userClass;
        $query = $query->from("{$userClass::tableName()} {$this->userAlias}");
        $noInitUser = $userClass::buildNoInitModel();
        /* @var $noInitUser User */
        $profileClass = $noInitUser->profileClass;
        if (!empty($profileClass)) {
            $query = $query->joinWith(["profile {$this->profileAlias}"]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageParam' => 'user-page',
                'defaultPageSize' => 20,
                'pageSizeParam' => 'user-per-page',
            ],
            'sort' => [
                'sortParam' => 'user-sort',
                'attributes' => [
                    'id',
                    'nickname',
                    'name' => [
                        'asc' => [$this->profileAlias . '.first_name' => SORT_ASC, $this->profileAlias . '.last_name' => SORT_ASC],
                        'desc' => [$this->profileAlias . '.first_name' => SORT_DESC, $this->profileAlias . '.last_name' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => Yii::t('user', 'Name'),
                    ],
                    'gender' => [
                        'asc' => [$this->profileAlias . '.gender' => SORT_ASC],
                        'desc' => [$this->profileAlias . '.gender' => SORT_DESC],
                        'default' => SORT_ASC,
                        'label' => Yii::t('user', 'Gender'),
                    ],
                    'createdAt' => [
                        'asc' => [$this->userAlias . '.created_at' => SORT_ASC],
                        'desc' => [$this->userAlias . '.created_at' => SORT_DESC],
                        'default' => SORT_ASC,
                        'label' => Yii::t('user', 'Creation Time'),
                    ],
                    'updatedAt' => [
                        'asc' => [$this->userAlias . '.updated_at' => SORT_ASC],
                        'desc' => [$this->userAlias . '.updated_at' => SORT_DESC],
                        'default' => SORT_ASC,
                        'label' => Yii::t('user', 'Last Updated Time'),
                    ],
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query = $query->andFilterWhere([
            'LIKE', $this->userAlias . '.id', $this->id,
        ])->andFilterWhere([
            'LIKE', $this->profileAlias . '.nickname', $this->nickname,
        ])->andFilterWhere([
            '>=', $this->userAlias . '.created_at', $this->createdFromInUtc,
        ])->andFilterWhere([
            '<=', $this->userAlias . '.created_at', $this->createdToInUtc,
        ])->andFilterWhere([
            'LIKE', $this->profileAlias . '.first_name', $this->first_name,
        ])->andFilterWhere([
            'LIKE', $this->profileAlias . '.last_name', $this->last_name,
        ])->andFilterWhere([
            $this->profileAlias . '.gender' => $this->gf,
        ]);
        $dataProvider->query = $query;
        return $dataProvider;
    }

    /**
     * Add `createdFrom` & `createdTo` attributes.
     * @return array
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['id'] = Yii::t('user', 'User ID');
        $attributeLabels['nickname'] = Yii::t('user', 'Nickname');
        $attributeLabels['first_name'] = Yii::t('user', 'First Name');
        $attributeLabels['last_name'] = Yii::t('user', 'Last Name');
        $attributeLabels['gf'] = Yii::t('user', 'Gender');
        $attributeLabels['createdFrom'] = Yii::t('user', 'From');
        $attributeLabels['createdTo'] = Yii::t('user', 'To');
        return $attributeLabels;
    }
}
