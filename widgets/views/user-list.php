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
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */
/* @var $additionalColumns array */
/* @var $actionColumn array */
/* @var $showGUID boolean */
$columns = [
    ['class' => SerialColumn::class],
    'guid' => [
        'class' => DataColumn::class,
        'header' => Yii::t('user', 'GUID'),
        'content' => function ($model, $key, $index, $column) {
            return $model->getReadableGUID();
        },
        'format' => 'text',
        'visible' => $showGUID,
    ],
    'id' => [
        'class' => DataColumn::class,
        'attribute' => 'id',
        'label' => Yii::t('user', 'User ID'),
        'content' => function ($model, $key, $index, $column) {
            /* @var $model User */
            return $model->id;
        },
        'contentOptions' => function ($model, $key, $index, $column) {
            /* @var $model User */
            if ($model->id != Yii::$app->user->identity->getID()) {
                return [];
            }
            return ['bgcolor' => '#00FF00'];
        },
    ],
    'nickname' => [
        'class' => DataColumn::class,
        'attribute' => 'nickname',
        'label' => Yii::t('user', 'Nickname'),
        'content' => function ($model, $key, $index, $column) {
            /* @var $model User */
            $profile = $model->profile;
            if (empty($profile)) {
                return null;
            }
            return $profile->nickname;
        },
    ],
    'name' => [
        'class' => DataColumn::class,
        'attribute' => 'name',
        'content' => function ($model, $key, $index, $column) {
            /* @var $model User */
            $profile = $model->profile;
            if (empty($profile)) {
                return null;
            }
            return $profile->first_name . ' ' . $profile->last_name;
        },
        'label' => Yii::t('user', 'Name'),
    ],
    'gender' => [
        'class' => DataColumn::class,
        'attribute' => 'gender',
        'label' => Yii::t('user', 'Gender'),
        'content' => function ($model, $key, $index, $column) {
            /* @var $model User */
            $profile = $model->profile;
            if (empty($profile)) {
                return null;
            }
            return Profile::getGenderDesc($profile->gender);
        }
    ],
    'createdAt' => [
        'class' => DataColumn::class,
        'attribute' => 'createdAt',
        'label' => Yii::t('user', 'Creation Time'),
        'content' => function ($model, $key, $index, $column) {
            /* @var $model User */
            return $column->grid->formatter->format($model->created_at, 'datetime');
        },
    ],
    'updatedAt' => [
        'class' => DataColumn::class,
        'attribute' => 'updatedAt',
        'label' => Yii::t('user', 'Last Updated Time'),
        'content' => function ($model, $key, $index, $column) {
            /* @var $model User */
            return $column->grid->formatter->format($model->updated_at, 'datetime');
        },
    ],
];
if (!empty($additionalColumns) && is_array($additionalColumns)) {
    $columns = array_merge($columns, $additionalColumns);
}
if (!empty($actionColumn)) {
    $columns[] = $actionColumn;
}
echo GridView::widget([
    'id' => 'user-grid-view',
    'caption' => Yii::t('user', 'Here are all the users who meet the search criteria:'),
    'dataProvider' => $dataProvider,
    'emptyText' => Yii::t('user', 'No users meet the search criteria found.'),
    'layout' => "{summary}\n<div class=\"table-responsive\">{items}</div>\n{pager}",
    'columns' => $columns,
    'tableOptions' => [
        'class' => 'table table-striped'
    ]
]);
?>
<div class="well well-sm">
    <?= Yii::t('user', 'User List Directions:') ?>
    <ol>
        <li><?= Yii::t('user', 'If no search criteria are specified, all users are displayed.') ?></li>
        <li><?= Yii::t('user', 'When the User ID column is green, it indicates that the user is the current logged-in user.') ?></li>
        <li><?= Yii::t('user', 'If the creation time is the same as the last update time, there is no change.') ?></li>
    </ol>
</div>
