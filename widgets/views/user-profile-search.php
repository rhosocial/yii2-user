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
use rhosocial\user\Profile;
use rhosocial\user\UserProfileSearch;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserProfileSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */
$css = <<<EOT
div.required label.control-label:after {
    content: " *";
    color: red;
}
EOT;
$this->registerCss($css);
?>

<div class="user-search">
    <?php $form = ActiveForm::begin([
        'id' => $formId,
        'action' => ['index'],
        'method' => 'post',
    ]); ?>
<div class="row">
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'id', [
        'template' => "{input}\n{error}",
    ])->textInput(['placeholder' => Yii::t('user', 'User ID'),]) ?>
    </div>
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'nickname', [
        'template' => "{input}\n{error}",
    ])->textInput(['placeholder' => Yii::t('user', 'Nickname'),]) ?>
    </div>
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'first_name', [
        'template' => "{input}\n{error}",
    ])->textInput(['placeholder' => Yii::t('user', 'First Name'),]) ?>
    </div>
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'last_name', [
        'template' => "{input}\n{error}",
    ])->textInput(['placeholder' => Yii::t('user', 'Last Name'),]) ?>
    </div>
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'createdFrom', [
        'template' => "{input}\n{hint}\n{error}",
    ])->widget(DateTimePicker::class, [
        'options' => ['placeholder' => Yii::t('user', 'From')],
        'pluginOptions' => [
            'todayHighlight' => true
        ]
    ])->hint(Yii::t('user', 'If you do not limit the start time, leave it blank.')) ?>
    </div>
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'createdTo', [
        'template' => "{input}\n{hint}\n{error}",
    ])->widget(DateTimePicker::class, [
        'options' => ['placeholder' => Yii::t('user', 'To')],
        'pluginOptions' => [
            'todayHighlight' => true
        ]
    ])->hint(Yii::t('user', 'If you do not limit the end time, leave it blank.')) ?>
    </div>
    <div class="col-md-3 col-sm-6">
    <?= $form->field($model, 'gf', [
        'template' => "{input}\n{error}",
    ])->dropDownList(Profile::getGenderDescsWithEmpty) ?>
    </div>
</div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('user', 'Search'), ['id' => "$formId-submit", 'class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('user', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
