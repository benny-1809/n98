Zum Installieren des moduls bitte folgende untere Befehle ausführen.

`composer config repositories.n98 github https://github.com/benny-1809/n98.git`

`composer require benny-1809/n98:dev-master`

`bin/magento setup:upgrade`

### Falls kein Grunt oder Gulp auf dem System vorhanden ist, bitte im Developer-Mode folgende folder löschen
`rm -rf var/view_preprocessed pub/static/frontend`

### Im Production-Mode bitte den static-content deployen für das greifen des _module.less files
`bin/magento setup:static-content:deploy -j2 en_US de_DE`

Danach können über http(s)://meine.domain/blog die Blog Artikel eingesehen werden.

