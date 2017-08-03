#!/bin/bash

# colors
COLOR_OFF="\033[0m"   # unsets color to term fg color
GREEN="\033[0;32m"    # green
CYAN="\033[0;36m"     # cyan

# heck php bin path, build image uses local
if test -f /usr/local/bin/php; then phppath='/usr/local/bin/php'; fi
if test -f /usr/bin/php; then phppath='/usr/bin/php'; fi

echo -e "${GREEN}Building PlinkerUI Javascript and CSS assets${COLOR_OFF}"

# clear first
$phppath ./vendor/bin/mini_asset clear --config ./public/template/assets.ini

# build
echo -e "${CYAN}-> Building ./public/template/assets.ini ${COLOR_OFF}"
$phppath ./vendor/bin/mini_asset build --config ./public/template/assets.ini
