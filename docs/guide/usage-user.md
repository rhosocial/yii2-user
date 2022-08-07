# Using the User model

## Concepts

- table name: `{{%user}}`
- The user's GUID & ID are automatically generated when registering.
- Only save password hash value, and the same password may not
mean the same hash.
- If the user has profile, its GUID is same as the user's.

## Preparation

- Create table for this model.
- In order to user your own `User` model, you should customize:
  - table name:`tableName()`
  - rules:`rules()`
  - attribute labels:`attributeLabels()`
  - behaviors:`behaviors()`

## Implement your own User model

Regardless of whether the current model to meet your needs, we do not recommend
that you use the model as a user identity model. You need to implement your own `User` model:

```php
class User extends \rhosocial\user\models\User
{
}
```

## Generate User GUID

This feature comes from the [`GUIDTrait`](https://github.com/rhosocial/yii2-base-models/blob/1.0.x/traits/GUIDTrait.php)
 of [`yii2-base-models`](https://github.com/rhosocial/yii2-base-models).

By default, the user GUID is the same as the GUID of other models, which is a
128-bit string, 16 bytes.

This value is used to uniquely identify a user. Therefore, in a relational
database, the column that holds the value should be set as the primary key; at
the same time, the table that holds the other user-related model should also
be referenced, that is, a model belongs to a user.

Based on the above description, we do not recommend that you modify this value
after the user registered. If you use a non-relational database, you will need
to ensure data consistency yourself.

## Generate User ID

By default, an 8-digit number starting with 4 is randomly generated during user
registration. Therefore, the ID attribute is empty before calling the
[[User::register()]] method.

The theoretical maximum number of users is 10 million, but we suggest that the
actual number of users should not exceed one-tenth of the theoretical value,
because when there are already many users, the probability of generating
random numbers will be high, in order to ensure uniqueness of ID, the frequency
of access to the database will be higher and higher.

If you do not want to use the default [`generateId()`](https://github.com/rhosocial/yii2-base-models/blob/1.0.x/traits/IDTrait.php#L128) 
method, you can inherit `User`
model and override it.

## Register a user

We do not recommend that you save the new user instance directly to the database,
please use the `register()` method instead.

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

If you want to assign the user permissions or roles, please pass them to second parameter
like following:

```php
try {
    $result = $user->register([$profile], ['Admin']);
} catch (\Exception $ex) {
}
```

If the return value is `true`, it means success.

If one of registration, saving associated models, and assigning permissions or roles
failed, the registration will not succeed. At the same time, the events registered with $eventRegisterFailed are called.

If the development mode is present, the exception itself is returned, or return false
if production mode is present.

The events registered with `$eventBeforeRegister` are called before the registration
process begins.

If the registration completes successfully, the events registered with `$eventAfterRegister`
are called before returning true.

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

### Get the last updated time

```php
$updatedAt = $user->updatedAt;
```

If you have defined a field named `updatedAt`, you should use `$user->getUpdatedAt()` method instead.

> Note: This attribute records the last time this user's attributes were modified and
does not involve its profile. If you want to know the last time the
profile modified, please refer to [Profile](usage-profile.md).

## Deregister a user

We also do not recommend that you delete the user from database directly,
please use `deregister()` method instead.

The `deregister()` method would throw exception when user de-registration failed,
so you should wrap it into try-catch block.

```php
try {
    $result = $user->deregister();
} catch (\Exception $ex) {
}
```

If the return value is true, it means success.

Similar to registration, the events registered with the $eventBeforeDeregister are called before deregistration.
If the unregister is successful, the events registered with $eventAfterDeregister are called, otherwise the event registered
 with $eventDeregisterFailed are called.

## Change password

### Change directly:

```php
$user->password = '<New Password>' // $password is write-only.
$user->save();                     // 
```

The `password` property is a magic one, it's originated from `setPassword()` method,
therefore it can only be set.

or
### Change password if reset token is valid:

First, we need to request a new password reset token:

```php
$result = $user->applyForNewPassword();
```

then, the $eventPasswordResetTokenGenerated event will be triggered.

you can access `$password_reset_token` attribute to obtain reset token and notify user by some ways
(e.g. email, SMS. the specific method requires your own implementation).

When a user provides new password, you also need to check the password reset token:

```php
$result = $user->resetPassword('<New Password>', '<Password Reset Token>');
```

This is to prevent data inconsistency, because it is possible that password reset token has been updated before reset new password.

## Use with Profile

### Create Profile

Each user can have a profile. We provide a default Profile class. [How to use it](usage-profile.md)

Before creating a `Profile`, you need to specify the name of the `Profile` class:

```php
$user->profileClass = Profile::class;
```

It is not associated with any Profile model by default.

When you create a Profile model, the `createProfile()` will check whether the `Profile` class exists in the current namespace.
If so, then use it. If not, the `rhosocial\user\models\Profile` will be used.

If they are not what you want, you can also customize the full qualified name of your `Profile` model.

### Get Profile

You can simply access `$user->profile` to get Profile model. It is a magic property defined by `getProfile()` method.

Note. If you want to get updated Profile model after profile updated, you should unset the `$user->profile` magic property first.

## Use with other models

If some of the models logically belong to a user (for example, the `Profile` described above),
in order to facilitate the secondary development, we make the following agreement:

### SubsidiaryMap

This feature comes from yii2-base-models.

You can add the class name and parameters of the model associated
with user to the `subsidiaryMap` property, like following:

```php
$user->addSubsidiary('article', [
    'class' => 'app\models\user\Article'
];
```

Or, you can directly define the `subsidiaryMap` property like following:

```php
public $subsidiaryMap = [
    'article' => [
        'class' => 'app\models\user\Article',
    ],
];
```

You need to make sure that the array keys are lowercase and that the defined
class does exist.

Then you can create `Article` model by magic-method, like following:

```php
$article = $user->createArticle(<Configuration Array>);
```

The author of the article has been set to the `$user`.

In order to facilitate secondary development, you can add the definition of
this magic method in the annotation.

### Active Record Relations

You need to define your own Active Record relations, like following:

```php
public function getArticles()
{
    return $this->hasMany($this->getSubsidiaryClass('article'), [
        'author_guid' => $this->guidAttribute,
    ]);
}
```

## Best Practices

- We do not recommend you do any changes on this class, unless you know the consequences of doing so.
If you feel that the functions are not enough or do not meet your
requirements, please implement a new `User` model and inherited from mine.
- Once the GUID generated, it is not recommended to modify it throughout
the user's lifecycle.
- Since the user ID is allowed to be modified, it is not recommended to use
the ID as a flag for fixing the user.
- If you feel that the default random assignment ID does not meet your
requirements, you can override the `generateId()` static method yourself.
- `status`, `type`, `source` attributes do not achieve specific functions,
these three need you according to the actual situation to develop the
corresponding functions.
- `pass_hash`, `created_at`, `updated_at`, `auth_key`, `access_token`,
`password_reset_token` are not recommended for direct accessing or
modification.
- `guid`, `pass_hash`, `auth_key`, `access_token`, `password_reset_token` are
sensitive data, you should prevent from exposing them to public.
