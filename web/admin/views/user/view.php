<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.me/
 * @copyright Copyright (c) 2016 - 2022 vistart
 * @license https://vistart.me/license/
 */
use rhosocial\user\models\User;
use rhosocial\user\models\Profile;
use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $user User */
$this->title = Yii::t('user', 'View User') . ' (' . $user->getID() . ')';
$this->params['breadcrumbs'][] = Yii::t('user', 'View User');
echo DetailView::widget([
    'model' => $user,
    'attributes' => [
        // 'GUID' => 'readableGUID', // The GUID is not displayed by default.
        'ID' => $user->idAttribute,
        // 'Password Hash' => $user->passwordHashAttribute, // The Password Hash is not displayed by default.
        'IP' => [
            'attribute' => 'ipAddress',
            'label' => Yii::t('user', 'IP Address'),
        ],
        'IP Type' => $user->ipTypeAttribute,
        'created_at' => [
            'attribute' => $user->createdAtAttribute,
            'format' => 'datetime',
        ],
        'updated_at' => [
            'attribute' => $user->updatedAtAttribute,
            'format' => 'datetime',
         ],
        'Authentication Key' => $user->authKeyAttribute,
        'Access Token' => $user->accessTokenAttribute,
        'Password Reset Token' => $user->passwordResetTokenAttribute,
        'Status' => $user->statusAttribute,
        'Type' => 'type',
        'Source' => $user->sourceAttribute,
    ],
]);

if (class_exists($user->profileClass) && ($profile = $user->profile)) {
    /* @var $profile Profile */
    echo DetailView::widget([
        'model' => $profile,
        'attributes' => [
            'nickname' => $profile->contentAttribute,
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'gender' => [
                'attribute' => 'gender',
                'label' => Yii::t('user', 'Gender'),
                'value' => function ($model, $widget) {
                    /* @var $model Profile */
                    /* @var $widget DetailView */
                    return $model->getGenderDesc($model->gender);
                },
            ],
            'gravatar_type' => 'gravatar_type',
            'gravatar' => 'gravatar',
            'timezone' => 'timezone',
            'individual_sign' => 'individual_sign',
            'created_at' => [
                'attribute' => $profile->createdAtAttribute,
                'format' => 'datetime',
            ],
            'updated_at' => [
                'attribute' => $profile->updatedAtAttribute,
                'format' => 'datetime',
             ],
        ],
    ]);
}?>
<h3><?= Yii::t('user', 'Other operations') ?></h3>
<div class="row">
    <div class="col-md-3">
        <?= Html::a(Yii::t('user', 'Back to User List'), [
            'index',
        ], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
