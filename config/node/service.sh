#!/bin/bash

env  NODE_ROOT_PATH
env  NODE_APP_NAME


appPath="/home/node/app"
installExpress(){
    mkdir $appPath/express && cd $appPath/express
    yarn add express

    cat <<'EOF' > express/main.js
    const express = require("express");
    const app = express();
    const hostname = "0.0.0.0";
    const port = 3000;
    app.get("/", (req, res) => {
            res.send("Hello World");
        });
    app.listen(port, () => {
            console.log(`Server running at http://${hostname}:${port}/`);
        });
    EOF
    }

if ! command -v pm2 &> /dev/null
then
    echo "Create sample and pm2 for your reference"
    yarn global add pm2
    installExpress
    exit
fi

cd /home/node/app

#---------Insert  you command to start Node application  Start----------

if test -f vuepress/main.js
then
    pm2 vuepress/main.js
fi

#---------Insert  you command to start Node application  End----------

tail -f /dev/null