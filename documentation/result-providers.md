# Result providers file

The applications only accepts requests from 'trusted' result providers. These providers must be configured in the result providers config file. This could a a json or yaml file.

## JSON

Json object containing the result providers. Each key in the object is a result provider and should contain a name and certificates property.

Example json format:

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

We created an example file result-providers.json.example. To get started you can copy this file to result-providers.json and edit it to your needs.

## YAML
> :warning: **  If you want to use yaml for the config file you need to enable the php-yaml extension.

Example yaml format:

```yaml
result-provider-1:
  name: "Result Provider 1"
  certificates:
  - cert: "<base64 encoded pem certificate>"
    chain: "<base64 encoded pem certificate chain>"
result-provider-2:
  name: "Result Provider 2"
  certificates:
  - cert: "<base64 encoded pem certificate>"
    chain: "<base64 encoded pem certificate chain>"
  - cert: "<base64 encoded pem certificate>"
    chain: "<base64 encoded pem certificate chain>"
```

See the [environment variables documentation](environment-variables.md#result-providers) for the necessary configuration. 
