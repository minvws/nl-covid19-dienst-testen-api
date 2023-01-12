# Lead Time (/api/test-results)

The lead time endpoint accepts information about the time from identification at the test facility till the time that the user has the test results.

## Fields

| Field                       | Type    | Required | Validation                                                                                                                                |
|-----------------------------|---------|----------|-------------------------------------------------------------------------------------------------------------------------------------------|
| Aanbieder                   | string  | Yes      | Value should be in our allow list and signing certificate of signature should match                                                       |
| TeststraatID                | string  | Yes      |                                                                                                                                           |
| Datum                       | string  | Yes      | Date string like: YYYY-MM-DD                                                                                                              |
| Testtype                    | string  | Yes      | Value or name from the [EU valueset for manufacturers](https://github.com/ehn-dcc-development/eu-dcc-valuesets/blob/main/test-manf.json). |
| GemTijdIdentificatieUitslag | integer | Yes      | `>= 0`                                                                                                                                    |
| GemTijdIdentificatieEmail   | integer | Yes      | `>= 0`                                                                                                                                    |

## Request

### Payload
```json
{
    "Aanbieder": "result-provider-1",
    "TeststraatID": "something",
    "Datum": "2023-01-10",
    "Testtype": "1232",
    "GemTijdIdentificatieUitslag": 1,
    "GemTijdIdentificatieEmail": 1
}
```


### Signed request
A request to the API should be signed. The CMS signing process has been documented in the [coronacheck provider docs](https://github.com/minvws/nl-covid19-coronacheck-provider-docs/blob/main/docs/providing-events-by-digid.md#cms-signature-algorithm), so it won't be repeated here.

Example of the request structure:
````
POST /api/test-realisation
Host: {endpoint}
Content-Type: application/json

{
    "payload": "<base64 encoded version of the payload>",
    "signature": "<base64 encoded version of the signature>"
}
````

## Response
The response of the application will be signed as well. It is possible for the application to return a 500 error, in which case the response possibly could not be signed.

### Success state
When everything went well en the request was accepted. The application will respond with a 200 OK.
````
HTTP/2 200 OK
Date: {}
Content-Type: application/json

{
    "payload": "<base64 encoded version of the payload>",
    "signature": "<base64 encoded version of the signature>"
}
````

#### Decoded payload
```json
{
    "success": true
}
```


### Error states
### 400 Bad Request
When the `Aanbieder` is not known or signature could not be validated with the known certificates of the `Aanbieder`, the application will respond with a 400 Bad Request.

````
HTTP/2 400 Bad Request
Date: {}
Content-Type: application/json

{
    "payload": "<base64 encoded version of the payload>",
    "signature": "<base64 encoded version of the signature>"
}
````

#### Decoded payload
```json
{
    "success": false
}
```


### 422 Unprocessable Entity
When the request is valid, but the payload is not valid, for example when a field validation gone wrong. The application will respond with a 422 Unprocessable Entity. The response will contain a list of errors that are wrong with the data.

````
HTTP/2 422 Unprocessable Content
Date: {}
Content-Type: application/json

{
    "payload": "<base64 encoded version of the payload>",
    "signature": "<base64 encoded version of the signature>"
}
````

#### Decoded payload
When there is an error on 1 field:
```json
{
    "message": "The datum field is required.",
    "errors": {
        "Datum": [
            "The datum field is required."
        ]
    }
}
```

When there are errors on multiple fields:
```json
{
    "message": "The datum field is required. (and 1 more error)",
    "errors": {
        "Datum": [
            "The datum field is required."
        ],
        "Testtype": [
            "The testtype field is required."
        ]
    }
}
```

### 500 Internal Server Error
When something internally in the application went wrong. The application will respond with a 500 Internal Server Error.

The response could be a little unpredictable and therefore we cannot provide a good example for it and you should act on the status code.
