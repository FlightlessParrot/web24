#!/bin/bash
COLOR='\033[0;32m' 
./vendor/bin/sail artisan orchid:admin admin admin@admin.com password;
echo -e "${COLOR}Admin has been created. The chosen is with us.${NC}";
