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
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider ActiveDataProvider */
$this->title = Yii::t('user', 'User List');
$this->params['breadcrumbs'][] = $this->title;
Pjax::begin([
    'id' => 'user-pjax',
]);
echo empty($dataProvider) ? '' : GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        /* The GUID is not displayed by default.
        'guid' => [
            'class' => DataColumn::class,
            'header' => Yii::t('user', 'GUID'),
            'content' => function ($model, $key, $index, $column) {
                return $model->getReadableGUID();
            },
            'format' => 'text',
        ],*/
        'id',
        'nickname' => [
            'class' => DataColumn::class,
            'header' => Yii::t('user', 'Nickname'),
            'value' => function ($data) {
                /* @var $data User */
                $profile = $data->profile;
                if (empty($profile) || !($profile instanceof Profile) || empty($profile->nickname)) {
                    return null;
                }
                return $profile->nickname;
            },
        ],
        'createdAt' => [
            'class' => DataColumn::class,
            'header' => Yii::t('user', 'Creation Time'),
            'content' => function ($model, $key, $index, $column) {
                /* @var $model User */
                return $column->grid->formatter->format($model->getCreatedAt(), 'datetime');
            },
        ],
        'updatedAt' => [
            'class' => DataColumn::class,
            'header' => Yii::t('user', 'Last Updated Time'),
            'content' => function ($model, $key, $index, $column) {
                /* @var $model User */
                return $column->grid->formatter->format($model->getUpdatedAt(), 'datetime');
            },
        ],
        [
            'class' => ActionColumn::class,
            'header' => Yii::t('user', 'Action'),
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
            },
            'visibleButtons' => [
                'view' => Yii::$app->user->can('viewUser'),
                'update' => Yii::$app->user->can('updateUser'),
                'delete' => Yii::$app->user->can('deleteUser'),
            ],
        ],
    ],
]);
Pjax::end();
?>
<div class="row">
    <div class="col-md-3">
        <?= Html::a(Yii::t('user', 'Register New User'), ['register-new-user'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
