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

use rhosocial\user\User;
use rhosocial\user\Profile;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider ActiveDataProvider */
Pjax::begin();
echo empty($dataProvider) ? '' : GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'guid' => [
            'class' => 'yii\grid\DataColumn',
            'header' => 'GUID',
            'content' => function ($model, $key, $index, $column) {
                /* @var $model User */
                return $model->getReadableGUID();
            },
            'format' => 'text',
        ],
        'id',
        'nickname' => [
            'class' => 'yii\grid\DataColumn',
            'header' => 'nickname',
            'value' => function ($data) {
                /* @var $data User */
                $profile = $data->profile;
                if (empty($profile) || !($profile instanceof Profile)) {
                    return '';
                }
                return $profile->nickname;
            },
        ],
        'created_at:datetime',
        'updated_at:datetime',
        [
            'class' => ActionColumn::class,
            'header' => 'Action',
            'urlCreator' => function (string $action, $model, $key, $index, ActionColumn $column) {
                /* @var $model User */
                if ($action == 'view') {
                    return Url::to(['view', 'id' => $model->getID()]);
                } elseif ($action == 'update') {
                    return Url::to(['update', 'id' => $model->getID()]);
                } elseif ($action == 'delete') {
                    return Url::to(['deregister', 'id' => $model->getID()]);
                }
                return '#';
            }
        ],
    ],
]);
Pjax::end();
