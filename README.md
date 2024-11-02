# Form for Uploading a File to WebDAV in PHP.
## Transfer web files to NextCloud storage

## Install

Example for FreeBSD:

### Redis:

```
$ pkg install redis
$ service redis enable

$ php -v
PHP 8.3.9 (cli) (built: Sep 21 2024 02:23:37) (NTS)

$ pkg install php83-pecl-redis
service php-fpm restart
```

### phpmailer:

```
composer --version
composer install
composer show


composer require phpmailer/phpmailer
```

## Configure

1. Prepare config.php

```
$ cp config/.config.php.default config/.config.php
```

2. Edit config.php

   Fill NextCloud data for connect

3. Configure NGINX.

   Main page `upload.php`

```nginx
  location ~ /\.(?!php).* {
    deny all;
    access_log off;
    log_not_found off;
  }

  location ~ ^/upload/.*\.php$ {
    root  /usr/local/www;
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
  }

  location /upload {
    root  /usr/local/www;
    index upload.php;
    try_files $uri $uri/ =404;
  }
```

## Example

1.
2.
3.
4.

## Progress bar version XHR

- 

NextCloud:

1.
2.
