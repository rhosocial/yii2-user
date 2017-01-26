# Using the User model

## Concepts

- table name: `{{%user}}`

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

If you enabled the IP Address features, it will automatically record the IP address
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

If you have defined a field named `updatedAt`, you should use `$user->getUpdatedAt()` method instead.

## Deregister a user

The `deregister()` method would throw exception when user de-registration failed,
so you should wrap it into try-catch block.

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

The `password` property is a magic one, it's origined from `setPassword()` method,
therefore it can only be set.

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

## Use with Profile

### Create Profile

Each user can have a profile. We provide a default Profile class. [How to use it](usage-profile.md)

Before creating a Profile, you need to specify the name of the Profile class:

```php
$user->profileClass = Profile::className();
```

It is not associated with any Profile model by default.

When you create a Profile model, the `createProfile()` will check whether the `Profile` class exists in the current namespace.
If so, then use it. If not, the `rhosocial\user\Profile` will be used.

If they are not what you want, you can also customize the full qualified name of your Profile model.

### Get Profile

You can simply access `$user->profile` to get Profile model. It is a magic property defined by `getProfile()` method.

Note. If you want to get updated Profile model after profile updated, you should unset the `$user->profile` magic property first.

## Best Practices

We do not recommend you do any changes on this class, unless you know the consequences of doing so.