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

## Add Test Users

```
yii user/add-test-users <total=1000> <password=123456>
```

We provide the command to add the test user, whose personal
information is generated randomly.

By default, 1000 users are generated at one time.
If you want to specify the number of users generated at once, you can add a number after the command, like following:

```
yii user/add-test-users 100
```

We suggest the number not too much, otherwise it may consume a long time.

After the command is run, each time 10 users are generated, a progress prompt is output.

Since the user ID needs to be unique, the registration time may be longer and
longer as the number of users increases, since the randomly generated ID may be
duplicated, so the database needs to be accessed repeatedly.

By default, the user ID is an 8-digit number beginning with 4, so the maximum number
of users is 10 million. But we recommend that the actual number of users should be
one-tenth of the theoretical value. Otherwise, the time spent by the registered user
may be unbearable.

Users registered with this command have the source attribute of `console_test`.
Therefore, remove the test user, in fact, is to deregister the user whose source
attribute is `console_test`.

## Register New User

We provide the command to register new user, the password
parameter is required.

```
yii user/register <password> [nickname] [firstName] [lastName]
```

Each of the last parameters is required if `Profile` model contains it.
