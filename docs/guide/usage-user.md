# Using the User model

## Concepts

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

## Deregister a user

```php
try {
    $result = $user->deregister();
} catch (\Exception $ex) {
}
```

## Change password

Change directly:

```php
$user->password = '<New Password>' // $password is write-only.
$user->save();                     // 
```

Change if password reset token is valid:

```php
$result = $user->applyForNewPassword();
```

```php
$result = $user->resetPassword('<New Password>', '<Password Reset Token>');
```

## Design Pattern