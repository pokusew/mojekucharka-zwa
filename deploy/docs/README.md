# phpDocumentor-generated docs deployment to Netlify

The phpDocumentor-generated documentation is deployed to **https://mojekucharka-zwa-docs.netlify.app/**
using [deploy.sh](./deploy.sh) script. It can be un-deployed (destroyed) using [destroy.sh](./destroy.sh). These scripts
can be invoked using make, see [Deployment](../../README.md#deployment) section in the project root README.

There are a few files that alter the behavior for this deployment:
* [netlify.toml](./netlify.toml) – [Netlify](https://www.netlify.com/) config
* [_headers](./_headers)
* [_redirects](./_redirects)
* [robots.txt](./robots.txt)
