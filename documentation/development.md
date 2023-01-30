# Development

## Configuration
The application needs environment variables, a result providers config file, a signing certificate and a result provider.


### Create .env file based of .env.example
```sh
cp .env.example .env
```

Edit `.env` to fill out the required environment variables.

[Read more about the environment variables](environment-variables.md)

### Create result-providers.json file
If you want to start with an example file you could copy the example file with the following command.
```sh
cp result-providers.json.example result-providers.json
```

[Read more about the result providers file](result-providers.md)

### Create signing certificates
The application needs a certificate to sign the API response and a result provider needs a certificate to sign the request to the API. 

Normally you use a certificate that is issued from a trusted certificate authority. But for development purposes you can create a self-signed certificate.

<details>

<summary>Create a certificate and chain</summary>

1. Create a private key for the CA.
```sh
# replace `ca.key` with a filename for the specific CA private key.
openssl genrsa -out ca.key 2048
```

2. Create a certificate to use as a CA.
```sh
# replace `ca.key` with the key file you created in step 1.
# replace `ca.crt` with a filename for the specific CA certificate.
# replace the `Common Name` with a name for the CA.
openssl req -x509 -new -nodes -key ca.key -subj "/CN=dienst-testen.ca/C=NL/L=Den Haag" -days 10000 -out ca.crt
```

3. Create a CSR for a certificate to be signed by the CA.
```sh
# this will create a certificate signing request (CSR) and outputs a private key file and a CSR file.
# replace `certificate.key` with a filename for the new created private key.
# replace `certificate.csr` with a filename for the new created csr.
# replace the `Common Name` with a name for the certificate.
openssl req -nodes -newkey rsa:2048 -keyout certificate.key -out certificate.csr -subj "/C=NL/L=Den Haag/CN=dienst-testen-sign.test"
```

4. Sign the CSR with the CA.
```sh
# this will sign a certificate signing request (CSR) with a CA certificate and outputs a signed certificate.
# replace `certificate.csr` with the csr file you created in step 3.
# replace `ca.crt` with the crt file you created in step 2.
# replace `ca.key` with the key file you created in step 1.
# replace `certificate.crt` with a filename for the new created certificate.
openssl x509 -req -in certificate.csr -CA ca.crt -CAkey ca.key -CAcreateserial -out certificate.crt -days 10000
```

</details>

<details>

<summary>Or use already existing test certificates</summary>

We supplied multiple certificates to be used in the unit and feature tests. While developing, you could also use these certificates.

You find those certificates in the `tests/fixtures/certificates` directory.

</details>

You could follow the above steps to create the signing certificate and chain for the API. Configure the certificate and chain in the `.env` file. [Read more about that here.](environment-variables.md#api-response-signing) 

### Create a result provider

For a result provider you also need a signing certificate. To create a certificate please read the section above, you could use the same command with different filenames.
When you have a certificate you will need to create a new entry in the result provider config file.

In the steps above the certificate is already in PEM format. So you only have to base64 encode the certificate and chain and add it to the result provider config file.
You can base64 encode a file with the following command.

```sh
base64 -w 0 certificate.crt
```

[Read more about the result providers file](result-providers.md)

## Run with docker-compose

Local requirements: `docker`, `docker-compose`, `openssl`, `composer`.

### Makefile
Using the included Makefile you can install dependencies and run the application with:

```sh
make sail
```

### Manually

Install the dependencies:

```sh
composer install
php artisan key:generate
php artisan ide-helper:generate
```

Then run the application via sail:
```sh
vendor/bin/sail up
```

By default, the application can be accessed at [http://localhost](http://localhost).

## Run another way (e.g. `php artisan serve`)

Local requirements: PHP 8.1 with `ext-json` and `ext-sodium`, `composer`.

### Makefile
Using the included Makefile you can install dependencies and run the application with:

```sh
make serve
```

### Manually


Install the dependencies:

```sh
composer install
php artisan key:generate
php artisan ide-helper:generate
```

Then run the application however you normally run PHP application, or with artisan:

```
php artisan serve
```

## Quality Assurance

This project is set up with linters (`phpcs`, `phpstan`, `psalm`) and tests (`Pest`).
To run `psalm`, you'll need to set up the ide-helper file once:

```sh
php artisan ide-helper:generate
```

Afterwards, you can run linters and tests respectively with:

```
make check
make test
```
