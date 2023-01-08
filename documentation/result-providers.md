# Result providers file

The applications only accepts requests from 'trusted' result providers. These providers must be configured in the result providers config file.

The result providers file is a json object containing the result providers. Each key in the object is a result provider and should contain a name and certificates property. For example:
```json
{
    "result-provider-1": {
        "name": "Result Provider 1",
        "certificates": [
            {
                "cert": "<base64 encoded pem certificate>",
                "chain": "<base64 encoded pem certificate chain>"
            }
        ]
    },
    "result-provider-2": {
        "name": "Result Provider 2",
        "certificates": [
            {
                "cert": "<base64 encoded pem certificate>",
                "chain": "<base64 encoded pem certificate chain>"
            },
            {
                "cert": "<base64 encoded pem certificate>",
                "chain": "<base64 encoded pem certificate chain>"
            }
        ]
    }
}
```

We created an example file result-providers.json.example. You can copy this file to result-providers.json and edit it to your needs.
You can name the file 
