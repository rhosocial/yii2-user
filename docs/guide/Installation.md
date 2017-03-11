# Installation

## Migrations

If you want to use built-in tables, you can execute built-in migrations (Only fit for MySQL).
Or you can create tables referenced by our provided SQL file (`vendor/rhosocial/yii2-user/tests/data/rhosocial_yii2_user.sql`), or migrations' comments.

Before you execute built-in migrations, you need to add our migration namespace
to the `migrationNamespaces` attribute of `migrate` controller in the `controllerMap`
attribute of the console application:
```
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        'migrationNamespaces' => [
            'rhosocial\user\migrations',
        ],
    ],
],
```

And specify `schemaMap` attribute of `Connection` component:
```
return [
    'class' => 'yii\db\Connection',
    ...
    'schemaMap' => [
        'mysql' => 'rhosocial\user\migrations\mysql\Schema',
    ],
];
```

Then you can execute the following command in the console:
```
yii migrate/up
```

You may see the following tips:
```
Total 2 new migrations to be applied:
        rhosocial\user\migrations\M170304140437CreateUserTable
        rhosocial\user\migrations\M170304142349CreateProfileTable

Apply the above migrations? (yes|no) [no]:
```

You can enter 'yes' to apply above migrations.

If you see more than two of those above migrations, you need to specify the migration like following:
```
yii migrate/to rhosocial\user\migrations\M170304140437CreateUserTable
```

## Authorization

### DbManager (Only Supports MySQL)

We provide you with a Role-Based Authorization Control manager and save the authorization information in the database (MySQL Only).

Before using the database manager, you need to perform the migrations:
```
yii migrate --migrationNamespaces=rhosocial\user\rbac\migrations
```

Then, you will see the following tips:
```
Yii Migration Tool (based on Yii v2.0.11.2)

Total 1 new migration to be applied:
        rhosocial\user\rbac\migrations\M170310150337CreateAuthTables

Apply the above migration? (yes|no) [no]:
```

If you want to apply the above migration, please enter `yes`.

If you see the following tips, it means successful:
```
*** applying rhosocial\user\rbac\migrations\M170310150337CreateAuthTables
    > create table {{%auth_rule}} ... done (time: 0.045s)
    > add primary key rule_name_pk on {{%auth_rule}} (name) ... done (time: 0.080s)
    > create table {{%auth_item}} ... done (time: 0.039s)
    > add primary key item_name_pk on {{%auth_item}} (name) ... done (time: 0.070s)
    > add foreign key rule_name_fk: {{%auth_item}} (rule_name) references {{%auth_rule}} (name) ... done (time: 0.079s)
    > create index idx-auth_item-type on {{%auth_item}} (type) ... done (time: 0.034s)
    > create table {{%auth_item_child}} ... done (time: 0.027s)
    > add primary key parent_child_pk on {{%auth_item_child}} (parent,child) ... done (time: 0.076s)
    > add foreign key parent_name_fk: {{%auth_item_child}} (parent) references {{%auth_item}} (name) ... done (time: 0.101s)
    > add foreign key child_name_fk: {{%auth_item_child}} (child) references {{%auth_item}} (name) ... done (time: 0.092s)
    > create table {{%auth_assignment}} ... done (time: 0.030s)
    > add primary key user_item_name_pk on {{%auth_assignment}} (item_name,user_guid) ... done (time: 0.056s)
    > add foreign key user_assignment_fk: {{%auth_assignment}} (user_guid) references {{%user}} (guid) ... done (time: 0.108s)
*** applied rhosocial\user\rbac\migrations\M170310150337CreateAuthTables (time: 0.866s)


1 migration was applied.

Migrated up successfully.
```

### PhpManager

