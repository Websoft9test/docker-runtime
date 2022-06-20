#!/bin/bash

cp /opt/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# edit supervisord.conf
# sed -i "s/autostart=*./autostart=false/g" /etc/supervisor/conf.d/supervisord.conf

# start by supervisord
/usr/bin/supervisord
