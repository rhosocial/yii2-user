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

use rhosocial\user\models\UserProfileView;
use rhosocial\user\widgets\UserProfileSearchWidget;
use rhosocial\user\widgets\UserListWidget;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel UserProfileView */
/* @var $dataProvider ActiveDataProvider */
/* @var $userListWidgetClass string */
/* @var $userSearchWidgetClass string */

$this->title = Yii::t('user', 'User List');
$this->params['breadcrumbs'][] = $this->title;

if (!class_exists($userListWidgetClass)) {
    $userListWidgetClass = UserListWidget::class;
}
if (!class_exists($userSearchWidgetClass)) {
    $userSearchWidgetClass = UserProfileSearchWidget::class;
}
$formId = 'user-search-form';
echo $userSearchWidgetClass::widget([
    'model' => $searchModel,
    'formId' => $formId,
]);
Pjax::begin([
    'id' => 'user-pjax',
    'formSelector' => "#$formId",
]);
echo $userListWidgetClass::widget(['dataProvider' => $dataProvider, 'actionColumn' => $userListWidgetClass::ACTION_COLUMN_DEFAULT]);
Pjax::end();
?>
<div class="row">
    <div class="col-md-3">
        <?= Html::a(Yii::t('user', 'Register New User'), ['register-new-user'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
