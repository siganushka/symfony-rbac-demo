# symfony-rbac-demo

### install

```php
$ git clone https://github.com/siganushka/symfony-rbac-demo.git
$ cd ./symfony-rbac-demo
$ composer install
```

### configure

```php
$ cp .env .env.local // setting local environment
$ vi .env.local // setting database
```

### create database && fixtures data

```php
$ php bin/console doctrine:database:create
$ php bin/console doctrine:schema:update --force
$ php bin/console doctrine:fixtures:load
```
