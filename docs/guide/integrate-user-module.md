# Integrate `User` Module

## Preparation

### Add `user` module definition to application configuration

You need to specify the class name of the `user` module in the 'modules' attribute of the application configuration:

```php
$config = [
    ...
    'modules' => [
        ...
        'user' => [
            'class' => 'rhosocial\user\web\user\Module',
        ],
        ...
    ],
    ...
];
```

If you want to attach more controllers or override existed controllers,
you can specify `controllerMap` property of `user` Module, like following:
```php
$config = [
    ...
    'modules' => [
        ...
        'user' => [
            'class' => 'rhosocial\user\web\user\Module',
            'controllerMap' => [
                'article' => [
                    'class' => <Article Controller Class>
                ],
                ...
            ],
        ],
    ],
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
            Yii::$app->user->isGuest ? ['label' => 'Login', 'url' => ['/user/auth/login']]
            : ['label' => Yii::t('user', 'Logout'), 'url' => ['/user/auth/logout'], 'linkOptions' => ['data-method' => 'post', 'data-pjax' => '0']],
        ],
    ]);
```