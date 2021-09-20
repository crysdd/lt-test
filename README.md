# Installation with docker:
```bash
sudo docker-compose up
```
This command create docker container, run migrations and serve laravel.

For make seeds in other terminal run
```bash
sudo docker exec <your_docker_container_name> php artisan db:seed
```
Add task scheduling:
```bash
sudo docker exec <your_docker_container_name> echo "* * * * * www-data /usr/bin/php /app/artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab
```

# Installation on bare metal:

Set settings in `.env`.

Add database settings.

Add encryption key (or set it on first launch app).

```bash
composer install
php artisan migrate
php artisan db:seed
php artisan serve
```
Add task scheduling:
```bash
echo "* * * * * www-data /usr/bin/php /app/artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab
```


All URI's has prefix `/api/v1`

`GET /users` - return paginated list of all users.

`POST /users/create` - make new user and return access api token. Fields: `name, email, password`.

`POST /login` - return token existing user.  Fields: `email, password`.

`GET /tasks` - return tasks of current user for this day.

`PUT /tasks/change-task` - re-released some task. Fields: `id`.

`PUT /tasks/done` - mark task as done. Fields: `id`.

