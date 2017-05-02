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

use yii\helpers\Html;
/* @var $id string */

$this->title = Yii::t('user', 'User Registered');
$this->params['breadcrumbs'] = [$this->title];

?>
<div class="jumbotron">
    <h1><?= Yii::t('user', 'Congratulations') ?></h1>

    <p class="lead"><?= Yii::t('user', 'You have successfully registered!') ?></p>
    <p class="lead"><?= Yii::t('user', 'Your ID is:') . $id ?></p>
    <p class="lead"><?= Yii::t('user', 'Please keep in mind your account!') ?></p>
    <p class="lead"><?= Yii::t('user', 'And use the password just fill in the registration:') ?></p>
    <p><?= Html::a(Yii::t('user', 'Login'), ['/user/auth/login', 'id' => $id]) ?></p>

</div>
