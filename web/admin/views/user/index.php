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

use rhosocial\user\UserProfileView;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel UserProfileView */
/* @var $dataProvider ActiveDataProvider */
$this->title = Yii::t('user', 'User List');
$this->params['breadcrumbs'][] = $this->title;
$formId = 'user-search-form';
Pjax::begin([
    'id' => 'user-pjax',
    'formSelector' => "#$formId",
]);
echo $this->render('_search', ['model' => $searchModel, 'formId' => $formId]);
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
        'nickname',
        'name' => [
            'class' => DataColumn::class,
            'header' => Yii::t('user', 'Name'),
            'content' => function ($model, $key, $index, $column) {
                return $model->last_name . $model->first_name;
            }
        ],
        'createdAt' => [
            'class' => DataColumn::class,
            'header' => Yii::t('user', 'Creation Time'),
            'content' => function ($model, $key, $index, $column) {
                /* @var $model User */
                return $column->grid->formatter->format($model->created_at, 'datetime');
            },
        ],
        'updatedAt' => [
            'class' => DataColumn::class,
            'header' => Yii::t('user', 'Last Updated Time'),
            'content' => function ($model, $key, $index, $column) {
                /* @var $model User */
                return $column->grid->formatter->format($model->updated_at, 'datetime');
            },
        ],
        [
            'class' => ActionColumn::class,
            'header' => Yii::t('user', 'Action'),
            'urlCreator' => function (string $action, $model, $key, $index, ActionColumn $column) {
                /* @var $model User */
                if ($action == 'view') {
                    return Url::to(['view', 'id' => $model->id]);
                } elseif ($action == 'update') {
                    return Url::to(['update', 'id' => $model->id]);
                } elseif ($action == 'delete') {
                    return Url::to(['deregister', 'id' => $model->id]);
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
