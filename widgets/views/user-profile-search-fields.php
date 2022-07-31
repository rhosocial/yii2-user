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
use rhosocial\user\UserSearch;
use yii\bootstrap5\ActiveForm;

/* @var $form ActiveForm */
/* @var $model UserSearch */
?>

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
    ])->dropDownList(Profile::getGenderDescsWithEmpty()) ?>
</div>
