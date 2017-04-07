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

use kartik\datetime\DateTimePicker;
use rhosocial\user\UserProfileSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserProfileSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */
?>

<div class="user-search">
    <?php $form = ActiveForm::begin([
        'id' => $formId,
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
<div class="row">
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'id') ?>
    </div>
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'nickname') ?>
    </div>
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'createdFrom')->widget(DateTimePicker::class, [
        'options' => ['placeholder' => Yii::t('user', 'From')],
        'pluginOptions' => [
            'todayHighlight' => true
        ]
    ]) ?>
    </div>
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'createdTo')->widget(DateTimePicker::class, [
        'options' => ['placeholder' => Yii::t('user', 'To')],
        'pluginOptions' => [
            'todayHighlight' => true
        ]
    ]) ?>
    </div>
</div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('user', 'Search'), ['id' => "$formId-submit", 'class' => 'btn btn-primary']) ?>
        <?= Html::submitButton(Yii::t('user', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
