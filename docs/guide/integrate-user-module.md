# Integrate `User` Module

## Preparation

### Add `user` module definition to application configuration

You need to specify the class name of the `user` module in the 'modules' attribute of the application configuration:

```php
$config = [
    ...
    'modules' => [
        'user' => [
            'class' => 'rhosocial\user\web\user\Module',
        ],
    ],
    ...
];
```

### Replace `login` & `logout` action with our own

`loginUrl` of `User` component:

```php
        'user' => [
            ...
            'loginUrl' => ['user/auth/login'],
            ...
        ],
```

`login` & `logout` link of view(s):

```php
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'About', 'url' => ['/site/about']],
            ['label' => 'Contact', 'url' => ['/site/contact']],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/user/auth/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/user/auth/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->getID() . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
```