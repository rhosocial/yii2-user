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

/* @var $panel yii\debug\panels\UserPanel */

use rhosocial\user\models\User;
use rhosocial\user\models\Profile;
use rhosocial\user\models\security\PasswordHistory;
use rhosocial\user\rbac\Item;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

?>

<h1>User Info</h1>

<?php if (!Yii::$app->user->isGuest) {
    $identity = $panel->data['identity'];
    /* @var $identity User */
    echo DetailView::widget([
        'model' => $panel->data['identity'],
        'attributes' => [
            'GUID' => 'readableGUID',
            'ID' => $identity->idAttribute,
            'Password Hash' => $identity->passwordHashAttribute,
            'IP' => [
                'attribute' => 'ipAddress',
                'label' => Yii::t('user', 'IP Address'),
            ],
            'IP Type' => $identity->ipTypeAttribute,
            'Created At' => [
                'attribute' => $identity->createdAtAttribute,
                'label' => Yii::t('user', 'Creation Time'),
                'value' => function($model, $widget) {
                    $value = Yii::$app->formatter->asDatetime($model->getCreatedAt());
                    $defaultTimeZone = Yii::$app->formatter->defaultTimeZone;
                    Yii::$app->formatter->defaultTimeZone = date_default_timezone_get();
                    $value .= ' (UTC: ' . Yii::$app->formatter->asDatetime($model->getCreatedAt()) . ' | Origial: ' . $model->getCreatedAt() . ')';
                    Yii::$app->formatter->defaultTimeZone = $defaultTimeZone;
                    return $value;
                },
            ],
            'Updated At' => [
                'attribute' => $identity->createdAtAttribute,
                'label' => Yii::t('user', 'Last Updated Time'),
                'value' => function($model, $widget) {
                    $value = Yii::$app->formatter->asDatetime($model->getUpdatedAt());
                    $defaultTimeZone = Yii::$app->formatter->defaultTimeZone;
                    Yii::$app->formatter->defaultTimeZone = date_default_timezone_get();
                    $value .= ' (UTC: ' . Yii::$app->formatter->asDatetime($model->getUpdatedAt()) . ' | Origial: ' . $model->getUpdatedAt() . ')';
                    Yii::$app->formatter->defaultTimeZone = $defaultTimeZone;
                    return $value;
                },
            ],
            'Authentication Key' => $identity->authKeyAttribute,
            'Access Token' => $identity->accessTokenAttribute,
            'Password Reset Token' => $identity->passwordResetTokenAttribute,
            'Status' => $identity->statusAttribute,
            'Type' => 'type',
            'Source' => $identity->sourceAttribute,
        ],
    ]);

    if (class_exists($identity->profileClass) && ($profile = $identity->profile)) {
        echo '<h2>Profile</h2>';
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
    }

    $history = $identity->passwordHistories;
    if (!empty($history)) {
        /* @var $history PasswordHistory[] */
        echo '<h2>Password History</h2>';

        $historyProvider = new ArrayDataProvider([
            'allModels' => $history,
        ]);
        $historyProvider->pagination->pageSize = 20;
        $historyProvider->pagination->pageParam = 'password-history-page';
        $historyProvider->sort->sortParam = 'password-history-sort';
        Pjax::begin([
            'id' => 'password-history-pjax',
        ]);
        echo GridView::widget([
            'dataProvider' => $historyProvider,
            'columns' => [
                'guid' => [
                    'class' => 'yii\grid\DataColumn',
                    'header' => 'Readable GUID',
                    'content' => function ($model, $key, $index, $column) {
                        return $model->getReadableGUID();
                    }
                ],
                'pass_hash',
                'createdAt:datetime',
            ],
        ]);
        Pjax::end();
    }

    if ($panel->data['rolesProvider']) {
        echo '<h2>Roles</h2>';
        Pjax::begin([
            'id' => 'role-pjax',
        ]);
        echo GridView::widget([
            'dataProvider' => $panel->data['rolesProvider'],
            'columns' => [
                'color' => [
                    'class' => 'yii\grid\Column',
                    'header' => 'Color',
                    'content' => function ($model, $key, $index, $column) {
                        /* @var $model Item */
                        if (!is_numeric($model->color)) {
                            return null;
                        }
                        $model->color = (int)($model->color);
                        if ($model->color < 0 || $model->color > 0xffffff) {
                            return null;
                        }
                        return "<font color=\"#fff\">" . dechex($model->color) . "</font>";
                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        /* @var $model Item */
                        if (!is_numeric($model->color)) {
                            return [];
                        }
                        $model->color = (int)($model->color);
                        if ($model->color < 0 || $model->color > 0xffffff) {
                            return [];
                        }
                        return ['bgcolor' => '#' . dechex($model->color)];
                    },
                ],
                'name',
                'description',
                'ruleName',
                'data',
                'createdAt:datetime',
                'updatedAt:datetime'
            ]
        ]);
        Pjax::end();
    }

    if ($panel->data['permissionsProvider']) {
        echo '<h2>Permissions</h2>';
        Pjax::begin([
            'id' => 'permission-pjax',
        ]);
        echo GridView::widget([
            'dataProvider' => $panel->data['permissionsProvider'],
            'columns' => [
                'color' => [
                    'class' => 'yii\grid\Column',
                    'header' => 'Color',
                    'content' => function ($model, $key, $index, $column) {
                        /* @var $model Item */
                        if (!is_numeric($model->color)) {
                            return null;
                        }
                        $model->color = (int)($model->color);
                        if ($model->color < 0 || $model->color > 0xffffff) {
                            return null;
                        }
                        return "<font color=\"#fff\">" . dechex($model->color) . "</font>";
                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        /* @var $model Item */
                        if (!is_numeric($model->color)) {
                            return [];
                        }
                        $model->color = (int)($model->color);
                        if ($model->color < 0 || $model->color > 0xffffff) {
                            return [];
                        }
                        return ['bgcolor' => '#' . dechex($model->color)];
                    },
                ],
                'name',
                'description',
                'ruleName',
                'data',
                'createdAt:datetime',
                'updatedAt:datetime'
            ]
        ]);
        Pjax::end();
    }

    if ($panel->data['loginLogProvider']) {
        echo '<h2>Login Logs</h2>';
        if (is_string($panel->data['loginLogProvider'])) {
            echo $panel->data['loginLogProvider'];
        } else {
            Pjax::begin([
                'id' => 'login-log-pjax',
            ]);
            echo GridView::widget([
                'dataProvider' => $panel->data['loginLogProvider'],
                'columns' => [
                    'guid' => [
                        'class' => 'yii\grid\Column',
                        'header' => 'GUID',
                        'content' => function ($model, $key, $index, $column) {
                            return $model->getReadableGUID();
                        },
                    ],
                    'id' => [
                        'class' => 'yii\grid\Column',
                        'header' => 'ID',
                        'content' => function ($model, $key, $index, $column) {
                            return $model->getID();
                        },
                    ],
                    'ip' => [
                        'class' => 'yii\grid\Column',
                        'header' => 'IP Address',
                        'content' => function ($model, $key, $index, $column) {
                            return $model->getIPAddress();
                        },
                    ],
                    'time' => [
                        'header' => 'Time',
                        'value' => function ($model, $key, $index, $column) {
                            return $model->getCreatedAt();
                        },
                        'format' => 'datetime',
                    ],
                    'device' => [
                        'class' => 'yii\grid\Column',
                        'header' => 'Device',
                        'content' => function ($model, $key, $index, $column) {
                            return $model->getDeviceDesc();
                        },
                    ],
                    'status' => [
                        'class' => 'yii\grid\Column',
                        'header' => 'Status',
                        'content' => function ($model, $key, $index, $column) {
                            $content = $model->getStatusDesc();
                            if ($model->status > 0) {
                                $content = "<font color=\"#f00\">" . $content . "</font>";
                            }
                            return $content;
                        },
                    ],
                ],
            ]);
            Pjax::end();
        }
    }
}
