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

    CreateAdminUser
    CreateUser
    DeleteAdminUser
    DeleteMyself
    DeleteUser
    UpdateAdminUser
    UpdateMyself
    UpdateUser

And we also provide the following roles (and their respective permissions):

    Admin       CreateUser, DeleteMyself(inherited from User), DeleteUser, UpdateMyself(inherited from User), UpdateUser
    User        DeleteMyself, UpdateMyself

We do not have to force a user to be assigned a role or permission,
nor do we check user roles or permissions when accessing `controller` and `action`.
It all needs you to do it yourself.

## References

- [Authorization - Official Docs](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html)