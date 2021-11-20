# Installation


## Prerequisites

 - Docker
 - Docker Compose
 - the commands specified in this doc assume you are a Linux user

## Install

Run the following commands (or their equivalent on your OS):

```
# create ENV file
cp .env.example .env
# make sure you add a value on the APP_KEY variable
# fill in the DB_PASSWORD variable as well (for local, you can find it in the docker-compose.override.yml file)

# build and run
docker-compose build
docker-compose up -d

# app setup
## install dev dependencies
docker-compose exec app composer install
## run migrations
docker-compose exec app php artisan migrate
```

Now the API should running and be accessible at `http://localhost`
