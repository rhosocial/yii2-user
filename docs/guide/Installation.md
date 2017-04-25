# Installation

## Migrations

If you want to use built-in tables, you can execute built-in migrations (Only fit for MySQL).
Or you can create tables referenced by our provided SQL file (`vendor/rhosocial/yii2-user/tests/data/rhosocial_yii2_user_all.sql`), or migrations' comments.

Before you execute built-in migrations, you need to create database, e.g. `yii2basic`,
and modify the `db` configuration first.

Then you can execute the following command in the console:
```
yii migrate/up --migrationPath=@vendor --migrationNamespaces=rhosocial\user\migrations --interactive=0
```

> Note: In the linux system, you may need to add additional escape characters(`\`).

You may see the following tips:
```
Yii Migration Tool (based on Yii v2.0.12-dev)

Total 3 new migrations to be applied:
	rhosocial\user\migrations\M170304140437CreateUserTable
	rhosocial\user\migrations\M170304142349CreateProfileTable
	rhosocial\user\migrations\M170307150614CreatePasswordHistoryTable

*** applying rhosocial\user\migrations\M170304140437CreateUserTable
    > create table {{%user}} ... done (time: 0.152s)
    > add primary key user_guid_pk on {{%user}} (guid) ... done (time: 0.157s)
    > create unique index user_id_unique on {{%user}} (id) ... done (time: 0.062s)
    > create index user_auth_key_normal on {{%user}} (auth_key) ... done (time: 0.055s)
    > create index user_access_token_normal on {{%user}} (access_token) ... done (time: 0.061s)
    > create index user_password_reset_token_normal on {{%user}} (password_reset_token) ... done (time: 0.070s)
    > create index user_created_at_normal on {{%user}} (created_at) ... done (time: 0.059s)
*** applied rhosocial\user\migrations\M170304140437CreateUserTable (time: 0.729s)

*** applying rhosocial\user\migrations\M170304142349CreateProfileTable
    > create table {{%profile}} ... done (time: 0.140s)
    > add primary key user_guid_profile_pk on {{%profile}} (guid) ... done (time: 0.117s)
    > add foreign key user_profile_fk: {{%profile}} (guid) references {{%user}} (guid) ... done (time: 0.133s)
*** applied rhosocial\user\migrations\M170304142349CreateProfileTable (time: 0.407s)

*** applying rhosocial\user\migrations\M170307150614CreatePasswordHistoryTable
    > create table {{%password_history}} ... done (time: 0.091s)
    > add primary key password_guid_pk on {{%password_history}} (guid) ... done (time: 0.097s)
    > add foreign key user_password_fk: {{%password_history}} (user_guid) references {{%user}} (guid) ... done (time: 0.152s)
*** applied rhosocial\user\migrations\M170307150614CreatePasswordHistoryTable (time: 0.348s)


3 migrations were applied.

Migrated up successfully.
```

It means successful.

## Authorization

### DbManager (Only Supports MySQL)

We provide you with a Role-Based Authorization Control manager and save the authorization information in the database (MySQL Only).

Before using the database manager, you need to perform the migrations:
```
yii migrate/up --migrationPath=@vendor --migrationNamespaces=rhosocial\user\rbac\migrations --interactive=0
```

> Note: In the linux system, you may need to add additional escape characters(`\`).

Then, you will see the following tips:
```
Yii Migration Tool (based on Yii v2.0.12-dev)

Total 1 new migration to be applied:
	rhosocial\user\rbac\migrations\M170310150337CreateAuthTables

*** applying rhosocial\user\rbac\migrations\M170310150337CreateAuthTables
    > create table {{%auth_rule}} ... done (time: 0.071s)
    > add primary key rule_name_pk on {{%auth_rule}} (name) ... done (time: 0.112s)
    > create table {{%auth_item}} ... done (time: 0.073s)
    > add primary key item_name_pk on {{%auth_item}} (name) ... done (time: 0.099s)
    > add foreign key rule_name_fk: {{%auth_item}} (rule_name) references {{%auth_rule}} (name) ... done (time: 0.159s)
    > create index idx-auth_item-type on {{%auth_item}} (type) ... done (time: 0.062s)
    > create table {{%auth_item_child}} ... done (time: 0.062s)
    > add primary key parent_child_pk on {{%auth_item_child}} (parent,child) ... done (time: 0.112s)
    > add foreign key parent_name_fk: {{%auth_item_child}} (parent) references {{%auth_item}} (name) ... done (time: 0.142s)
    > add foreign key child_name_fk: {{%auth_item_child}} (child) references {{%auth_item}} (name) ... done (time: 0.173s)
    > create table {{%auth_assignment}} ... done (time: 0.065s)
    > add primary key user_item_name_pk on {{%auth_assignment}} (item_name,user_guid) ... done (time: 0.129s)
    > add foreign key user_assignment_fk: {{%auth_assignment}} (user_guid) references {{%user}} (guid) ... done (time: 0.144s)
*** applied rhosocial\user\rbac\migrations\M170310150337CreateAuthTables (time: 1.628s)


1 migration was applied.

Migrated up successfully.
```
It means successful.

