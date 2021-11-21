# API Documentation

## Authorization

The CRUD endpoints are protected with an API key authentication, in order to prevent exposing sensitive data to any type of user. See [Installation](installation.md) guide for documentation on how to get API keys.

After getting a key, the APIs protected this way (see routes covered by the `apiKeyAuth` middleware in `routes/api.php`) expect an `Api-Key` header with the key to be sent with every request. NOTE: this is not the user authentication.

## Authentication

Users authorization is done through JSON Web Tokens.

## Endpoints

See included [Postman collection](Grapefruit.ro Tech Challenge - Paul Ghiuzan.postman_collection.json).
