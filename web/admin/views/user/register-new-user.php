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
use rhosocial\user\forms\RegisterForm;
use rhosocial\user\widgets\RegisterFormWidget;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model RegisterForm */
$this->title = Yii::t('user', 'Register New User');
$this->params['breadcrumbs'][] = $this->title;
echo RegisterFormWidget::widget(['model' => $model]);
?>
<h3><?= Yii::t('user', 'Other operations') ?></h3>
<div class="row">
    <div class="col-md-3">
        <?= Html::a(Yii::t('user', 'Back to User List'), [
            'index',
        ], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
