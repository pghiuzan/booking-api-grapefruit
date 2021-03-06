# Booking API - Grapefruit Interview

Paul Ghiuzan's implementation of the Booking API technical challenge

## Topics

 - [Installation](docs/installation.md)
 - [API docs](docs/api_docs.md)

## Testing

Run `docker-compose exec vendor/bin/phpunit` to run the tests.
*NOTICE:* the functional tests will affect the DB (creating and deleting records)

## TODOs

1. Write unit tests, currently covered with functional tests
2. There seems to be a problem using the `tymon/jwt-auth` package in Lumen tests, receiving an error saying `Auth driver [jwt] for guard [api] is not defined.`. It works fine when running the application normally.
3. The login attempts rate limiter could be copied/inherited from Laravel, instead of writing a custom solution for it.
4. Add Elasticsearch for optimized trips search/filter. The full text search part in particular is implemented using case-insensitive DB level text search which stands no chance in production environments.
5. Better documentation for the API
6. Maybe more validations/constraints are necessary on the resources data

## License

See [LICENSE](LICENSE)
