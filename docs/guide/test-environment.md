# Environment

We have preset the following test environments:

- [PHP 8.0, MySQL 8.0](../../tests/environments/yii2-user-php80/mysql8.yml)
- [PHP 8.1, MySQL 8.0](../../tests/environments/yii2-user-php81/mysql8.yml)

If you need to execute tests locally, you can use `docker-compose` to deploy the environment.
E.g.

```shell
$ docker-compose -f tests/environments/yii2-user-php80/mysql8.yml up -d --build
```

Since the docker image used does not include [phpunit](https://phpunit.de), this command will install phpunit on top of it.