#!/bin/bash

echo -e "\033[0;32mSetting correct file permissions\033[0m"

chown www-data:www-data ./ -R

echo -e "\033[0;32mRe running composer dump-autoload to fix Tasks namespace\033[0m"

composer du
