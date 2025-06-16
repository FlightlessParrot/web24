#!/bin/bash
COLOR='\033[0;32m'
NC='\033[0m'

echo 'Stopping apache'
sudo service apache2 stop
echo 'Stopping mysql'
sudo service mysql stop

echo -e "${COLOR}I killed them, my Shrimp.${NC}"
echo ""
