# Dienst Testen API

API for test providers to send details about test realisations, test results and lead times.

## API Endpoints

Information about the API endpoints can be found in the [API endpoints](documentation/api/endpoints.md).

## Other endpoints

| Endpoint | Description                       |
|----------|-----------------------------------|
| /        | Simple plaintext website title    |
| /health  | Information about service status  |

### Health check endpoint

The health check endpoint can be used to check if the API is up and running. The endpoint is available at `/health`. The
endpoint returns a `200` status code when the API is up and running, and a `503` if the service is unavailable.

In addition, the endpoint returns a JSON response containing two fields: `healthy` and `externals`. The `healthy` field
will be `true` if the API is up and running, and `false` if the service is unavailable. The `externals` field will
be `true` if the API is up and running and all external services are available, and `false` if the service is
unavailable or one or more external services are unavailable.

## Development

For development, see [development.md](documentation/development.md).

## Environments

The following environments are currently up and running.

| Domain                                                       | Environment |
|--------------------------------------------------------------|-------------|
| [api.diensttesten.nl](https://api.diensttesten.nl)           | Production  |
| [api.acc.diensttesten.nl](https://api.acc.diensttesten.nl)   | Acceptance  |
| [api.test.diensttesten.nl](https://api.test.diensttesten.nl) | Test        |
