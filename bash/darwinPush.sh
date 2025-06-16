#!/bin/bash

COLOR='\033[0;32m' 
NC='\033[0m'

echo "I am preparing your code for Darwin push";
echo "Update languages.";

./vendor/bin/sail artisan lang:update

echo "Pint the code";
./vendor/bin/sail pint;

echo "Code validation.";
./vendor/bin/sail bin phpstan || { echo "PHPStan failed"; exit 1; };

echo "Tests coverage";
./vendor/bin/sail pest --coverage || { echo "Pest tests failed"; exit 1; };

echo -e "${COLOR}We are prepared.${NC}"; 
