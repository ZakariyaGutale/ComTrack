# ComTrack

This repository contains the open source version of the ComTrack applications (backoffice and map viewer) used to manage and share information about commitments made in the scope of a particular project/initiative.

---

## DEVELOPMENT SETUP

To ease the development the project provides a docker setup that includes all the necessary components in order to quickly have a running development environment. Follow the next steps to properly setup your ComTrack environment

1. Install the necessary dependencies (git, docker and docker-compose)
2. Use git to clone this repo
3. Navigate into the docker folder and issue the following commands:

   - `docker-compose build mariadb`
   - `docker-compose build apache`
   - `docker-compose up -d mariadb apache scheduler mailhog`  

4. Install php dependencies in the apache container

   - `docker exec -it ooc-server bash`
   - `cd /var/www/html/backoffice`  
   - `php composer.phar install`
   - `exit`

5. Edit your hosts file (in linux this file is /etc/hosts) and add the apache container ip (you can get this ip by analysing the output of: `docker network inspect docker_ooc`). As an example, this file should look like the following:

    ```bash
        192.168.1.5 ooc-backoffice.com
        192.168.1.5 ooc-datahub.com
    ```

6. You will most probably need to  adjust the permissions (giving write privileges) to the following folders:

   - `www/uploads`
   - `www/backoffice/assets/images/ooc`
   - `www/datahub/json/beneficiaries`
   - `www/datahub/json/projects`

After this, you should be able to access:  

- [Backoffice application - poc login](http://www.ooc-backoffice.com)
- [Backoffice application - host login](http://www.ooc-backoffice.com/hosts/login) 
- [Datahub application - map viewer](http://www.ooc-datahub.com)  
- [Mailhog fake SMTP server outbox](http://localhost:8025)

If you wish to have a web interface to access the db directly, PhpMyAdmin is also provided within the docker setup. To have it up and running execute `docker-compose up -d phpadmin` and access it on [phpMyAdmin](http://localhost:8082/). In case you want to use your own db client, please refer to **USERS AND PASSWORDS** section and use the following configurations:

```
host: localhost
port: 3306
database: dg_mare
```

---

## USERS AND PASSWORDS

| Application | User | Password | Role |
|--- |--- |--- |--- |
| Backoffice | admin@ooc.com | admin | administrator/host |
| Backoffice | noreply@ooc.com | system | system user for sending emails and other automated tasks. **NOT TO BE USED AS NORMAL USER!** |
| Database | ooc | ooc | db admin |

**NOTE**: the passwords are stored using MD5 encryption. You can generate new passwords through the application or manually by issuing `echo -n mynewpassword | md5sum` and pasting the result in the db.

---

## VIEWING LOGS

- MariaDB: `docker logs -f ooc-db`
- Apache server: `docker logs -f ooc-server`

---

## ACCESSING CONTAINERS

- MariaDB: `docker container exec -it ooc-db bash`
- Apache  server: `docker container exec -it ooc-server bash`
  
---

## CONFIGURATION OF THE BACKOFFICE APPLICATION

The `config.env` file available in `www/backoffice/` folder should be configured according to the desired environment. The provided file contains all the configurations for the development docker setup. This file must always be edited when manually deploying the application in another server. The options are as follows:

- Application configs
  - **BO_APP_ENV**: the target environment for the application. Values can be `development` or `production`
  - **BO_APP_URL**: the base url of the application (e.g. `http://www.ooc-backoffice.com/`).
  - **BO_APP_COOKIE_DOMAIN**: to be set to your domain (e.g `.ooc-backoffice.com`)
  - **BO_APP_COOKIE_PREFIX**: to setup a prefix for cookies if you need to avoid collisions (default value is `ooc`)
  - **BO_APP_COOKIES_NAME**: the name of the cookie session (default value is `ooc_session`)
  - **BO_APP_COOKIE_CONSENT_NAME**: the name of the cookie consent (default value is `ooc_consent`)
  - **BO_APP_TIMEZONE**: set the default timezone that should be used by the application. Check the [List of supported timezones](http://php.net/manual/en/timezones.php)
  - **BO_APP_UPLOADS**: the path to the folder to store uploaded files (e.g. `/var/www/html/uploads/`)
  - **BO_APP_REPORTS**: the path to the folder to store report and log files (e.g. `/var/www/html/logs/`)
  - **BO_EMAIL_FROM**: the email used by the system to send emails (e.g. `no-reply@somedomain.com`)
  - **BO_EMAIL_FROM_NAME**: the name used by the system as the sender of emails (e.g. `Our Ocean Conference`)
  - **BO_CAPTCHA_SITE_KEY**: the site key provided after registering your site with [Google reCaptcha](https://www.google.com/recaptcha/)
  - **BO_CAPTCHA_SECRET_KEY**: the secret key provided after registering your site with [Google reCaptcha](https://www.google.com/recaptcha/)

- Export configs
  - **DH_APP_URL**: the base url for the datahub application
  - **DH_MAPS_FOLDER**: the path to the folder where the aplication is stored
  - **DH_JSON_EXPORT_FOLDER**: the path to the folder where the json files will be stored
  - **DH_JSON_ITEMS_PER_FILE**: number of items per json file

- Mail configs
  - **MAIL_HOST**: SMTP host server name (e.g. `smtp.gmail.com`)
  - **MAIL_USER**: SMTP user name
  - **MAIL_PASS**: SMTP user password
  - **MAIL_PORT**: SMTP hosts server port (by default `465`)
  - **MAIL_PROTOCOL**: SMTP protocol (by default `ssl`)

- Database configs
  - **BO_DB_HOST**: the hostname of the DB server
  - **BO_DB_NAME**: the name of the DB
  - **BO_DB_USER**: the username to be used to connect to the DB
  - **BO_DB_PASSWORD**: the password to be used to connect to the DB

All options are mandatory and must be defined.
