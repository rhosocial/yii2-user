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
/* @var $this yii\web\View */
/* @var $model RegisterForm */
$this->title = Yii::t('user', 'Register New User');
$this->params['breadcrumbs'][] = $this->title;
echo RegisterFormWidget::widget(['model' => $model]);
