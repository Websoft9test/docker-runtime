#!/bin/bash

installApp(){
   
    mkdir -p $1/$2  && cd $1/$2
    yarn add express

cat > $1/$2/main.js <<-EOF
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

# install pm2 and setup runtime project
echo "Create sample and pm2 for your reference"
yarn global add pm2
installApp $NODE_ROOT_PATH $NODE_APP_NAME

if [ $2  -eq "express"];then

elif [ $2  -eq "xxx"];then

else
  echo "Not support APP:$2 now!"
fi

cd $1
pm2 $2/main.js

tail -f /dev/null
