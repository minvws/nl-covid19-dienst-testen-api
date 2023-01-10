# Environment Variables

You can provide environment variables via the `.env` file. To get started you can copy the `.env.example` file to `.env` and edit it to your needs.

## API Response Signing
The response of the api endpoints is cryptographically signed.
[Click here to read more about the response signing.](api-response-signing.md)

| Variable            | Description                                              | Default |
|---------------------|----------------------------------------------------------|---------|
| CMS_SIGN_X509_CERT  | Path to the CMS signing certificate in PEM format.       |         |
| CMS_SIGN_X509_KEY   | Path to the CMS signing key in PEM format.               |         |
| CMS_SIGN_X509_PASS  | Password for the CMS signing certificate.                |         |
| CMS_SIGN_X509_CHAIN | Path to the CMS signing certificate chain in PEM format. |         |

## Result providers
The application needs a list of trusted result providers. These providers must be configured in the result providers config file. This could a a json or yaml file. 
[Click here to read more about the result providers file.](result-providers.md)

| Variable                      | Description                                           | Default                                    |
|-------------------------------|-------------------------------------------------------|--------------------------------------------|
| RESULT_PROVIDERS_CONFIG_PATH  | Path to the result providers config file.             |                                            |
| RESULT_PROVIDERS_STORAGE_PATH | Path to save the data that the result providers sent. | /var/www/html/storage/app/result-providers |

## Value sets
For validating the test manufacturers we load the value sets in the application.
For this we need the following variables.

| Variable                                       | Description                                                                    | Default                                                |
|------------------------------------------------|--------------------------------------------------------------------------------|--------------------------------------------------------|
| CORONA_CHECK_VALUE_SETS_URL                    | Url to the value sets.                                                         | https://verifier-api.coronacheck.nl/v8/dcbs/value_sets |
| CORONA_CHECK_VALUE_SETS_CACHE_TTL              | Number of secconds to cache the value sets.                                    | 900                                                    |
| CORONA_CHECK_VALUE_SETS_CERTIFICATE_FILE_PATHS | Path to the certificates of the value sets to check the signature and payload. |                                                        |
| CORONA_CHECK_PROXY                             | Proxy for guzzle to access the value sets.                                     |                                                        |

