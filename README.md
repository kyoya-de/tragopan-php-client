# Tragpopan PHP-CLI-Client
This utility is the command line client for my [tragopan](https://github.com/kyoya-de/tragopan) project.
It can be used to fetch a SSL certificate automatically, e.g. while provisioning a VM.

## Usage
1. Download the PHAR file.
2. Create a JSON configuration file.
```json
{
  "url":"http://yourdoamin-or-ip/",
  "api-key":"IAmNotSecure",
  "defaults":{
    "files": {
      "ca": {
        "cert":"my-awesome-ca.crt"
      },
      "server":{
        "key":"my-awesome-server.key",
        "cert":"my-awesome-server.crt"
      }
    },
    "cert":{
      "country":"DE",
      "state":"Germany",
      "locality":"Berlin",
      "organization":"Awesome Company CA",
      "organizationalUnit":"Development Special Forces",
      "name":"My Awesome VM"
    },
    "host":"my-awesome.vm"
  }
}
```
3. Run the command `php tragopan.phar download:server`.

If you need the certificate of you own CA, just run `php tragopan.phar download:ca`.
