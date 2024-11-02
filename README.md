# Project: Secure Temporary File Upload Service

This project provides a secure, temporary file-upload service with time-limited access. It generates a unique URL for file uploads, includes CAPTCHA protection, and sends the URL to the user via email. The following is a list of key functionalities and features implemented in this project.

## Key Features

1. **Secure URL Registration**  
   - Generates a unique URL for each request.
   - Protects URL generation with CAPTCHA to prevent abuse.

2. **Time-Limited URL Access**  
   - URL expires 48 hours after creation.
   - URL is sent only to the provided email address.
   - After the URL is activated, the expiration time reduces to 1 hour for security.

3. **User File Upload**  
   - Allows users to upload files up to 1GB.
   - Displays upload progress, including elapsed and remaining time estimates.

4. **File Storage on NextCloud**  
   - Uploaded files are stored in a fixed folder within NextCloud storage.
   - Original filenames are replaced with a new generated name for privacy.

5. **Metadata Generation**  
   - A `.txt` metadata file is created for each uploaded file.
   - The metadata file contains the original filename and the sender’s email address.


--


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

## Examples:

1. ![Знімок екрана 2024-10-18 013839](https://github.com/user-attachments/assets/88acccde-9687-4562-8e29-6e0e64044be2)

2. ![Знімок екрана 2024-10-18 013943](https://github.com/user-attachments/assets/650a6c4d-e090-4a70-be13-ab6f2817ae2e)

3. ![Знімок екрана 2024-10-18 014026](https://github.com/user-attachments/assets/decb04aa-6981-477f-b9a8-096c2b6e37bc)

4. ![Знімок екрана 2024-10-18 014201](https://github.com/user-attachments/assets/2d5bb553-5f7d-409f-a6d7-a05e451d4d6b)
5. ![Знімок екрана 2024-10-18 014255](https://github.com/user-attachments/assets/7e872aef-76f9-4ad0-823a-8ded9155f4f8)
6. ![Знімок екрана 2024-10-18 013725](https://github.com/user-attachments/assets/0152b2e4-723e-45e2-8861-50446e2f5d1e)
7. ![Знімок екрана 2024-10-18 013803](https://github.com/user-attachments/assets/b967a265-2cfd-433f-81ca-c5e0eb5d790b)


NextCloud:

1. ![375911888-1d9f3833-e9b3-431c-aba5-644bf00f63c3](https://github.com/user-attachments/assets/d90d6c35-d06a-4bc3-b328-81e7831a806f)

2. ![375911906-6915cbcd-e221-4ea8-96ba-46213e8b63ec](https://github.com/user-attachments/assets/c202d6e3-586b-4152-835a-5a2c6747556c)

