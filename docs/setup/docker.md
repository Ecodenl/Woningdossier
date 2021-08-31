# Setting up with Docker

Prerequisites:
- Docker

## Set up
```
docker-compose up -d --build site
docker-compose exec php php /var/www/html/artisan migrate:fresh --seed
```

## Credits
This is based on (and thanks to) the excellent [blogpost of Andrew Schmelyun](https://dev.to/aschmelyun/the-beauty-of-docker-for-local-laravel-development-13c0) and the accompanying [github repo](https://github.com/aschmelyun/docker-compose-laravel).