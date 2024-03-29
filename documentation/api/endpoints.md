# API Endpoints
The application has the following endpoints:

| Endpoint                                     | Description                                                                                                                        |
|----------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------|
| [/api/test-realisation](test-realisation.md) | Deliver information about the number of booked and done tests on a specific date and hour.                                         |
| [/api/test-results](test-results.md)         | Deliver information about the results numbers of the done tests on a specific date.                                                |
| [/api/lead-time](lead-time.md)               | Deliver information about the lead time from identification at the test facility till the time that the user has the test results. |

You can click on the endpoint to go to the endpoint specific documentation, there you will find the validation rules for example. 

## Authentication of the result provider
The result provider sends json object in the following format:
```json
{
    "payload": "<base64 encoded json payload>",
    "signature": "<base64 encoded cms signature of the json payload>"
}
```

<details>
  <summary>Example of the data a result provider needs to send</summary>

```json
{
    "payload": "eyJBYW5iaWVkZXIiOiJhYW5iaWVkZXItMTIzIiwiVGVzdHN0cmFhdElEIjoiQUFCQkJDQ0NEREQiLCJEYXR1bSI6IjIwMjItMTItMjEiLCJUZXN0dHlwZSI6IkFiYm90dCBSYXBpZCBEaWFnbm9zdGljcywgUGFuYmlvIENvdmlkLTE5IEFnIFJhcGlkIFRlc3QiLCJHZW1UaWpkSWRlbnRpZmljYXRpZVVpdHNsYWciOjY0MDIsIkdlbVRpamRJZGVudGlmaWNhdGllRW1haWwiOjk5OH0K",
    "signature": "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"
}
```
</details>

The json payload always need contain the `Aanbieder` field. The value of this field is the key of the result provider in the [result providers config file](../result-providers.md) (allow list of providers). Based on the `Aanbieder` field we check if the signature is signed with the specified certificate and chain that is specified in the allow list, also the signature is checked against the provided payload.
So we know the data is coming from a result provider that is allowed to send data to the API and that the payload is not tampered.

### New result provider
After completing the procedure at Dienst Testen, please email [helpdesk@rdobeheer.nl]( mailto:helpdesk@rdobeheer.nl?subject=Aansluiten%20dienst%20testen%20API) with your signing certificate and chain certificate. We will add your certificate to the allow list and you can start sending data to the API.

## Signing requests
The API requires a request to be signed. The CMS signing process is the same as CoronaCheck and has been documented in the [coronacheck provider docs](https://github.com/minvws/nl-covid19-coronacheck-provider-docs/blob/main/docs/providing-events-by-digid.md#cms-signature-algorithm).

For reference the signing process by hand is described below.

1. Create a payload file
```json
{"Aanbieder":"Teststraat Test","TeststraatID":"AABBBCCCDDD","Datum":"2022-12-23","Testtype":"Abbott Rapid Diagnostics, Panbio Covid-19 Ag Rapid Test","GemTijdIdentificatieUitslag":2,"GemTijdIdentificatieEmail":1}
```

2. Sign payload and create base64 encoded signature
```sh
# payload.json is a file containing the json payload
# certificate.crt is the signing certificate
# chain.crt should be a chain of certificates including the CA certificate
# certificate.key is the private key of the signing certificate
openssl cms -in payload.json -sign -outform DER -signer certificate.crt -certfile chain.crt -inkey certificate.key | base64 -w 0
```

3. Create a request body
```json
{
    "payload": "<base64 encoded version of the payload>",
    "signature": "<base64 encoded version of the signature>"
}
```
