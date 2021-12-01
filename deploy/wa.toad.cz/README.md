# wa.toad.cz

[wa.toad.cz](https://wa.toad.cz/) is a [web-server for students' semestral projects][ctu-zwa-wa-toad-cz]
in the CTU's B6B39ZWA course.

The app is deployed to https://wa.toad.cz/~endlemar/ using [deploy.sh](./deploy.sh) script. It can be un-deployed
(destroyed) using [destroy.sh](./destroy.sh). These scripts can be invoked using make,
see [Deployment](../../README.md#deployment) section in the project root README.

There are a few files that alter the app behavior for this deployment:
* [.htaccess](./.htaccess) – Apache web server config
* [index.php](./index.php)
* config.local.php – must be created in this directory before deployment

[ctu-zwa-wa-toad-cz]: https://cw.fel.cvut.cz/wiki/courses/b6b39zwa/tutorials/01/start#webserver
