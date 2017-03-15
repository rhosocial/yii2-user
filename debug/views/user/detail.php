<?php

/* @var $panel yii\debug\panels\UserPanel */

use rhosocial\user\User;
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
                'name',
                'description',
                'ruleName',
                'data',
                'createdAt:datetime',
                'updatedAt:datetime'
            ]
        ]);
    }

} ?>

