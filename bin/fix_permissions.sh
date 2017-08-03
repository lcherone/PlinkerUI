#!/bin/bash

echo -e "\033[0;32mSetting correct file permissions\033[0m"

chown www-data:www-data ./ -R
