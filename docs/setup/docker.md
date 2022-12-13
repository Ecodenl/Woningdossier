# Setting up with Docker (Sail)

Prerequisites:
- Docker
- Composer
  
We will assume you have aliased `./vendor/bin/sail` to `sail`.

## Set up
```
composer install --ignore-platform-reqs
sail build --no-cache
sail up -d
sail composer install
sail artisan key:generate
sail artisan migrate:fresh --seed
```