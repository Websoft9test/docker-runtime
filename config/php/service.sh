#!/bin/bash

cp /opt/config/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
chmod +x /etc/supervisor/conf.d/supervisord.conf

# edit supervisord.conf
# sed -i "s/autostart=*./autostart=false/g" /etc/supervisor/conf.d/supervisord.conf

# access app workdir
cd $APP_NAME-app

# start by supervisord
/usr/bin/supervisord
