<?php

/* @var $panel yii\debug\panels\UserPanel */

use rhosocial\user\User;
use rhosocial\user\Profile;
use rhosocial\user\security\PasswordHistory;
use rhosocial\user\rbac\Item;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\widgets\DetailView;

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
            'IP' => $identity->ipAttribute,
            'IP Type' => $identity->ipTypeAttribute,
            'Created At' => $identity->createdAtAttribute,
            'Updated At' => $identity->updatedAtAttribute,
            'Authentication Key' => $identity->authKeyAttribute,
            'Access Token' => $identity->accessTokenAttribute,
            'Password Reset Token' => $identity->passwordResetTokenAttribute,
            'Status' => $identity->statusAttribute,
            'Type' => 'type',
            'Source' => $identity->sourceAttribute,
        ],
    ]);
    
    if (class_exists($identity->profileClass) && ($profile = $identity->profile)) {
        /* @var $profile Profile */
        echo DetailView::widget([
            'model' => $profile,
            'attributes' => [
                'nickname' => 'nickname',
                'first_name' => 'first_name',
                'last_name' => 'last_name',
                'gender' => 'gender',
                'individual_sign' => 'individual_sign',
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
        
        echo GridView::widget([
            'dataProvider' => $historyProvider,
            'columns' => [
                'guid' => [
                    'class' => 'yii\grid\Column',
                    'content' => function ($model, $key, $index, $column) {
                        return $model->getReadableGUID();
                    }
                ],
                'pass_hash',
                'createdAt:datetime',
            ],
        ]);
    }

    if ($panel->data['rolesProvider']) {
        echo '<h2>Roles</h2>';

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
    }

    if ($panel->data['permissionsProvider']) {
        echo '<h2>Permissions</h2>';

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
    }

    if ($panel->data['loginLogProvider']) {
        echo '<h2>Login Logs</h2>';
        if (is_string($panel->data['loginLogProvider'])) {
            echo $panel->data['loginLogProvider'];
        } else {
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
                        'class' => 'yii\grid\Column',
                        'header' => 'Time',
                        'content' => function ($model, $key, $index, $column) {
                            return $model->getCreatedAt();
                        },
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
        }
    }
}
