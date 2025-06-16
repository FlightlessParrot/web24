#!/bin/bash
COLOR='\033[0;32m'
NC='\033[0m'

echo 'Stop mysql and apache [true]? (true or 1)'
read stop;
if [ "$stop" == 'true' -o "$stop" == '1' -o "$stop" = "" ];
then bash ./bash/stopServices.sh;
fi;
systemctl --user start docker-desktop

echo "Waiting for Docker to be ready..."
MAX_ATTEMPTS=120
attempt=0
while ! docker info >/dev/null 2>&1; do
    if [ $attempt -ge $MAX_ATTEMPTS ]; then
        echo "Error: Docker didn't start within the expected time!"
        exit 1
    fi
    attempt=$((attempt + 1))
    sleep 1
done
echo -e "${COLOR}I am calling on your servants, my Shrimp.${NC}"
echo 'starting sail';
./vendor/bin/sail up -d;
echo 'Starting node in the sail'
./vendor/bin/sail npm run dev;
