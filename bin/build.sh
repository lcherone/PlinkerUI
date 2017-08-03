#!/bin/bash

# Colors
COLOR_OFF="\033[0m"   # unsets color to term fg color
GREEN="\033[0;32m"    # green
CYAN="\033[0;36m"     # cyan

# Check php bin path, build image uses local
if test -f /usr/local/bin/php; then phppath='/usr/local/bin/php'; fi
if test -f /usr/bin/php; then phppath='/usr/bin/php'; fi

echo -e "${GREEN}Building Javascript and CSS assets${COLOR_OFF}"

echo -e "${CYAN}---> Clearing asset cache${COLOR_OFF}"
$phppath ./vendor/bin/mini_asset clear --config ./app/assets.ini

echo -e "${CYAN}---> Building main ./app/assets.ini ${COLOR_OFF}"
$phppath ./vendor/bin/mini_asset build --config ./app/assets.ini

#FILES=$(find ./app/modules/* -type f -name '*.ini')
#for f in $FILES
#do
#  echo -e "${CYAN}---> Building module:${COLOR_OFF} $phppath ./vendor/bin/mini_asset build --config $f"
#  $phppath ./vendor/bin/mini_asset build --config $f
#done