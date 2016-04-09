# Using the User model

## Concepts

- table name: '{{%user}}'

## Preparation

- Create table for this model.
- Implement your own User model.
  - Customize table name.
  - Customize rules.
  - Customize attribute labels.
  - Customize behaviors.

## Implement your own User model

Regardless of whether the current model to meet your needs, we do not recommend
that you use the model as a user identity model. You need to implement your own user model:

```php
class User extends \rhosocial\user\User
{
}
```

## Register a user

```php
try {
    $result = $user->register();
} catch (\Exception $ex) {
}
```

If the user model also has other subsidiary model needs to be written to the
database at the time of registration, you can pass the models to register() method.

```php
try {
    $result = $user->register([$profile]);
} catch (\Exception $ex) {
}
```

### Get IP Address used for registration

if you enabled the IP Address features, it will automatically record the IP address
of the user when registering. After that, you can use the following statement to
get it:

```php
$ipAddress = $user->ipAddress;
```

You will get the standard form of IP Address (IPv4 address in dotted decimal or IPv6 address in colon hexadecimal).

If you have defined a field named `ipAddress`, you should use `$user->getIpAddress()` method instead.

### Get registration time

```php
$createdAt = $user->createdAt;
```

If you have defined a field named `createdAt`, you should use `$user->getCreatedAt()` method instead.

### Get the last update time

```php
$updatedAt = $user->updatedAt;
```

if you have defined a field named `updatedAt`, you should use `$user->getUpdatedAt()` method instead.

## Deregister a user

```php
try {
    $result = $user->deregister();
} catch (\Exception $ex) {
}
```

## Change password

### Change directly:

```php
$user->password = '<New Password>' // $password is write-only.
$user->save();                     // 
```

or
### Change password if reset token is valid:

First, we need to request a new password reset token:

```php
$result = $user->applyForNewPassword();
```

then, the $eventPasswordResetTokenGenerated event will be triggered.

you can access `$password_reset_token` attribute to obtain reset token and notify user by some ways (e.g. email, SMS).

When a user provides new password, you also need to check the password reset token:

```php
$result = $user->resetPassword('<New Password>', '<Password Reset Token>');
```

This is to prevent data inconsistency, because it is possible that password reset token has been updated before reset new password.

## Best Practices

We do not recommend you do any changes on this class, unless you know the consequences of doing so.