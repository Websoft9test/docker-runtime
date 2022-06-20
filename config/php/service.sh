#!/bin/bash

# edit supervisord.conf
# sed -i "s/autostart=*./autostart=false/g" /etc/supervisor/conf.d/supervisord.conf

# start by supervisord
/usr/bin/supervisord
