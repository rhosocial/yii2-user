# Using the User Commands

We provide you with the `user` commands, to facilitate your user system for the most simple operations.

## Preparation

You need to add the namespace of the `user` command and user identity class to
the `controllerMap` property of configuration of the console application:

```php
...
    'controllerMap' => [
        ...
        'user' => [
            'class' => 'rhosocial\user\console\controllers\UserController',
            'userClass' => '<You own user identity class>',
        ],
        ...
    ],
...
```