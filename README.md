## Run with Makefile

> **!!!CAUTION**: Makefile uses tabs instead of spaces for indentation

```shell
make deploy
```

## Run without Makefile

* Docker env
```
$ cp .env-example .env
```

* Laravel env
```
$ cd src
$ cp .env.example .env
```

```
$ docker-compose up -d --build
$ docker-compose exec showroom chown -R www-data:www-data ./
$ docker-compose exec showroom chmod -R g+w ./storage
```

```
$ docker-compose exec showroom composer install
```

* jwtシークレットを生成する

```
$ docker-compose exec showroom php artisan key:generate
$ docker-compose exec showroom php artisan jwt:secret
$ docker-compose exec showroom php artisan storage:link
$ docker-compose exec showroom php artisan migrate
$ docker-compose exec showroom php artisan db:seed


```

* update env
```
$ docker-compose exec showroom php artisan config:cache
```
URL：http://127.0.0.1:10080

#### DB

* MySQL Workbench

| --- | --- |
| Host | 127.0.0.1 |
| Port | 13308 |
| User | admin |
| Pass | secret|

