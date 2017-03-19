<?php

/* @var $panel yii\debug\panels\UserPanel */

use rhosocial\user\User;
use rhosocial\user\rbac\Item;
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

}
