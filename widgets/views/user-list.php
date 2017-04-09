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

use rhosocial\user\Profile;
use rhosocial\user\User;
use rhosocial\user\UserProfileSearch;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */
/* @var $actionColumn array */
$columns = [
    ['class' => SerialColumn::class],
    /* The GUID is not displayed by default.
    'guid' => [
        'class' => DataColumn::class,
        'header' => Yii::t('user', 'GUID'),
        'content' => function ($model, $key, $index, $column) {
            return $model->getReadableGUID();
        },
        'format' => 'text',
    ],*/
    'id' => [
        'class' => DataColumn::class,
        'attribute' => 'id',
        'label' => Yii::t('user', 'User ID'),
        'content' => function ($model, $key, $index, $column) {
            /* @var $model UserProfileSearch */
            return $model->id;
        },
        'contentOptions' => function ($model, $key, $index, $column) {
            /* @var $model UserProfileSearch */
            if ($model->id != Yii::$app->user->identity->getID()) {
                return [];
            }
            return ['bgcolor' => '#00FF00'];
        },
    ],
    'nickname',
    'name' => [
        'class' => DataColumn::class,
        'attribute' => 'name',
        'content' => function ($model, $key, $index, $column) {
            return $model->last_name . $model->first_name;
        },
        'label' => Yii::t('user', 'Name'),
    ],
    'gender' => [
        'class' => DataColumn::class,
        'attribute' => 'gender',
        'label' => Yii::t('user', 'Gender'),
        'content' => function ($model, $key, $index, $column) {
            return Profile::getGenderDesc($model->gender);
        }
    ],
    'createdAt' => [
        'class' => DataColumn::class,
        'attribute' => 'createdAt',
        'label' => Yii::t('user', 'Creation Time'),
        'content' => function ($model, $key, $index, $column) {
            /* @var $model UserProfileSearch */
            return $column->grid->formatter->format($model->created_at, 'datetime');
        },
    ],
    'updatedAt' => [
        'class' => DataColumn::class,
        'attribute' => 'updatedAt',
        'label' => Yii::t('user', 'Last Updated Time'),
        'content' => function ($model, $key, $index, $column) {
            /* @var $model UserProfileSearch */
            return $column->grid->formatter->format($model->updated_at, 'datetime');
        },
    ],
];
if (!empty($actionColumn)) {
    $columns[] = $actionColumn;
}
echo GridView::widget([
    'id' => 'user-grid-view',
    'caption' => Yii::t('user', 'Here are all the users who meet the search criteria:'),
    'dataProvider' => $dataProvider,
    'layout' => "{summary}\n<div class=\"table-responsive\">{items}</div>\n{pager}",
    'columns' => $columns,
    'tableOptions' => [
        'class' => 'table table-striped'
    ]
]);
