# Using the Debug Panel

We provide you with a simple debugging panel for displaying the most basic user
information, such as `User` model, role & permission assignments.

## Preparation

We recommend that you replace the `User` panel provided by `yii2-debug` repo with
our own:

```php
$config['modules']['debug'] = [
    ...
    'panels' => [
        ...
        'user' => [
            'class' => 'rhosocial\user\debug\panels\UserPanel'
        ],
        ...
    ],
    ...
];
```