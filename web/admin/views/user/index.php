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
use rhosocial\user\widgets\UserProfileSearchWidget;
use rhosocial\user\widgets\UserListWidget;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel UserProfileView */
/* @var $dataProvider ActiveDataProvider */
$this->title = Yii::t('user', 'User List');
$this->params['breadcrumbs'][] = $this->title;
$formId = 'user-search-form';
echo UserProfileSearchWidget::widget([
    'model' => $searchModel,
    'formId' => $formId,
]);
Pjax::begin([
    'id' => 'user-pjax',
    'formSelector' => "#$formId",
]);
echo UserListWidget::widget(['dataProvider' => $dataProvider, 'actionColumn' => UserListWidget::ACTION_COLUMN_DEFAULT]);
Pjax::end();
?>
<div class="well well-sm">
    <?= Yii::t('user', 'Directions:') ?>
    <ol>
        <li><?= Yii::t('user', 'If no search criteria are specified, all users are displayed.') ?></li>
        <li><?= Yii::t('user', 'When the User ID column is green, it indicates that the user is the current logged-in user.') ?></li>
        <li><?= Yii::t('user', 'If the creation time is the same as the last update time, there is no change.') ?></li>
    </ol>
</div>
<div class="row">
    <div class="col-md-3">
        <?= Html::a(Yii::t('user', 'Register New User'), ['register-new-user'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
