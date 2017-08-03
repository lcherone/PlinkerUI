#/bin/bash

main() {
    #
    if (whiptail --title "PlinkerUI - v0.0.1" --yesno "Would you like to setup PlinkerUI?" 8 78) then

        menu=`menu`
        
        #
        if [ "$menu" == "<-- Back" ]; then
            main
        fi
        
        #
        if [ "$menu" == "Fix Permissions" ]; then
            permissions
        fi
        
        #
        if [ "$menu" == "Set Crontab" ]; then
            cronjob
        fi
        
         #
        if [ "$menu" == "Minify Assets" ]; then
            minify
        fi

    else
        echo "PlinkerUI setup exited, no changes have been made."
    fi
}

menu() {
    local menu=$(whiptail --title "PlinkerUI - v0.0.1" --menu "What you would like to setup?" 20 78 8 \
        "<-- Back" "Return to the main menu." \
        "Fix Permissions" "Correctly sets owner and permissions on PlinkerUI project files." \
        "Set Crontab" "Set crontab for daemon." \
        "Minify Assets" "Minify PlinkerUI CSS and Javascript assets." 3>&1 1>&2 2>&3)
        
    echo "$menu"
}

permissions() {
    chown www-data:www-data ./ -R
}

cronjob() {
    crontab -l | { cat; echo "\n* * * * * cd `pwd`/tasks && /usr/bin/php `pwd`/tasks/run.php >/dev/null 2>&1"; } | crontab -
}

minify() {
    # php bin path, build image uses local
    if test -f /usr/local/bin/php; then phppath='/usr/local/bin/php'; fi
    if test -f /usr/bin/php; then phppath='/usr/bin/php'; fi
    
    echo -e "Building PlinkerUI Javascript and CSS assets."
    
    # clear first
    $phppath ./vendor/bin/mini_asset clear --config ./public/template/assets.ini
    
    # build
    echo -e "Building ./public/template/assets.ini"
    $phppath ./vendor/bin/mini_asset build --config ./public/template/assets.ini
}

main
