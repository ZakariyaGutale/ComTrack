#!/bin/sh
set -e

role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then
    exec apache2-foreground

elif [ "$role" = "scheduler" ]; then
    while [ true ]
    do
      cron -f
      #/usr/local/bin/php /var/www/html/backoffice/ooc-cron.php
      sleep 60
    done

else
    echo "Could not match the container role \"$role\""
    exit 1

fi