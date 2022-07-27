# Using Role-Base Authorization Control Features

## Preparation

We only provide database-based RBAC features. So you need to create the corresponding data tables.

The data tables that need to be created are as follows (according to the order of creation):

- `{{%auth_rule}}`
- `{{%auth_item}}`
- `{{%auth_item_child}}`
- `{{%auth_assignment}}`

Each schema of the above database tables is slightly different from the official data table provided by Yii.

The specific change is to replace the user ID with the user GUID, and add `failed_at` column to the `Assignment` schema.

For details, please refer to the comments provided by our [migration script](../../rbac/migrations/M170310150337CreateAuthTables.php).

This migration script adds several role and permission definitions after creating the above database tables.

We provide the following permissions:

    GrantAdmin
    CreateUser
    RevokeAdmin
    DeleteMyself
    DeleteUser
    UpdateAdmin
    UpdateMyself
    UpdateUser

And we also provide the following roles (and their respective permissions):

    Webmaster      GrantAdmin, UpdateAdmin, RevokeAdmin, and all permissions inherited from `Admin`.
    Admin          CreateUser, DeleteUser, UpdateUser, and all permission inherited from `User`.
    User(default)  DeleteMyself, UpdateMyself.

We do not have to force a user to be assigned a role or permission,
nor do we check user roles or permissions when accessing `controller` and `action`.
It all needs you to do it yourself, like following.

### Application Configuration

It needs to cooperate with our own `DbManager`:

```
    ...
    'components' => [
        'authManager' => [
            'class' => 'rhosocial\user\rbac\DbManager',
        ],
    ],
    ...
```

## Assign or Revoke Role / Permission

### Assign a role when registering

It allows to assign roles or permissions when registering:

```php
...
use rhosocial\user\rbac\roles\User as UserRole;
...
$profile = $user->createProfile(['nickname' => 'vistart', 'first_name' => 'vistart', 'last_name' => 'zhao']);
$role = new UserRole();
try {
    $user->register([$profile], [$role]);
} catch (\Exception $ex) {
    ...
}
...
```

### Assign a role in general

```php
Yii::$app->authManager->assign((new UserRole())->name, $this->user);
```

By default, there is no deadline for each role given.
If you want to set a validity period to above role, and make it failed after expiration, you can assign the third parameter:

```php
Yii::$app->authManager->assign((new UserRole())->name, $this->user, '2017-05-05 00:00:00");
```

The third parameter is formatted as 'Y-m-d H:i:s'.

When you access a role or permission, it checks whether the role or permission has expired first.
If it has expired, it is equivalent to not having this role or does not have this permission.

### Revoke a role

```php
Yii::$app->authManager->revoke((new UserRole())->name, $this->user);
```

By default, when this user is deleted, all roles or permissions that are assigned will be revoked.

## References

- [Authorization - Official Docs](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html)