#!/bin/sh
# v2.0.0

DIR=${PWD}
DIR_NAME=${PWD##*/}

if [ "$DIR" == "scripts" ]; then
    DIR=$(cd ..;pwd)
    DIR_NAME=${DIR##*/}
fi

DOMAIN=${DIR_NAME}.test
ENV_FILE=$DIR/env.php
WP=$DIR/vendor/bin/wp
WP_CORE_DIR=$DIR/public/wp
WP_PATH=--path=$WP_CORE_DIR

DEFAULT_DB_PASS=dev
DEFAULT_DB_PREFIX=wp_
DEFAULT_DB_USER=dev
DEFAULT_WP_EMAIL=dev@${DOMAIN}
DEFAULT_WP_PASS=dev
DEFAULT_WP_USER=dev

BOLD=`tput bold`
GREEN=`tput setaf 10`
GREY=`tput setaf 8`
NORMAL=`tput sgr0`
RED=`tput setaf 9`
UNDERLINE=`tput smul`
WHITE=`tput setaf 15`
YELLOW=`tput setaf 3`

check_errors() {
    ERRORS=()
    # Check that mysql is installed
    if ! which mysql > /dev/null; then
        ERRORS=("${ERRORS[@]}" "==>$WHITE mysql ${GREY}${UNDERLINE}https://dev.mysql.com/downloads/mysql/${NORMAL}")
    fi
    # Check that perl is installed
    if ! which perl > /dev/null; then
        ERRORS=("${ERRORS[@]}" "==>${WHITE} perl  ${GREY}${UNDERLINE}https://www.perl.org/get.html${NORMAL}")
    fi
    if [ ${#ERRORS[@]} -gt 0 ]; then
        echo "${RED}${BOLD}Error: ${NORMAL}The following commands were not found:"
        for error in "${ERRORS[@]}"
            do
                echo $"${error}"
        done
        exit 1
    fi
}

get_config() {
    if [ ! -f $ENV_FILE ]; then
        cp $DIR/env.example.php $ENV_FILE
    fi
    DB_NAME=$(perl -lne 'm{DB_NAME.*?([\w.-]+)} and print $1' $ENV_FILE)
    DB_USER=$(perl -lne 'm{DB_USER.*?([\w.-]+)} and print $1' $ENV_FILE)
    DB_PASS=$(perl -lne 'm{DB_PASSWORD.*?([\w.-]+)} and print $1' $ENV_FILE)
    DB_PREFIX=$(perl -lne 'm{table_prefix.*?([\w.-]+)} and print $1' $ENV_FILE)
}

set_config() {
    # perl -i -wne 'print unless /(AUTH_KEY|SECURE_AUTH_KEY|LOGGED_IN_KEY|NONCE_KEY|AUTH_SALT|SECURE_AUTH_SALT|LOGGED_IN_SALT|NONCE_SALT)/' $ENV_FILE
    perl -i -pe "s|example|$DIR_NAME|g" $DIR/deploy/config.yml
    perl -i -pe "s|example|$DIR_NAME|g" $DIR/deploy/hosts.yml
    perl -i -pe "s|example|$DIR_NAME|g" $ENV_FILE
    perl -i -pwe "/DB_NAME/ && s|'$DB_NAME'|'$DBNAME'|" $ENV_FILE
    perl -i -pwe "/DB_USER/ && s|'$DB_USER'|'$DBUSER'|" $ENV_FILE
    perl -i -pwe "/DB_PASS/ && s|'$DB_PASS'|'$DBPASS'|" $ENV_FILE
    perl -i -pwe "/table_prefix/ && s|'$DB_PREFIX'|'$DBPREFIX'|" $ENV_FILE
    perl -i -pe 'BEGIN {
        @chars = ("a" .. "z", "A" .. "Z", 0 .. 9);
        push @chars, split //, "!@#$%^&*()-_ []{}<>~\`+=,.;:/?|";
        sub salt { join "", map $chars[ rand @chars ], 1 .. 64 }
    }
    s/put your unique phrase here/salt()/ge' $ENV_FILE
}

install_wp() {
    $WP db create $WP_PATH > /dev/null 2>&1
    if ! $WP core is-installed $WP_PATH; then
        $WP core install $WP_PATH --url="http://$DOMAIN" --title="$DIR_NAME" --admin_user="$WPUSER" --admin_password="$WPPASS" --admin_email="$WPEMAIL"
        # set options
        $WP option update welcome 0 $WP_PATH
        $WP option update uploads_use_yearmonth_folders 0 $WP_PATH
        $WP option update blogdescription "" $WP_PATH
        $WP option update permalink_structure "/%postname%/" $WP_PATH
        $WP option update show_on_front page $WP_PATH
        $WP option update page_on_front 2 $WP_PATH
        $WP post delete 1 --force $WP_PATH
        $WP post update 2 --post_title=Home --post_name=home --post_content= $WP_PATH
        $WP user meta update 1 show_welcome_panel 0 $WP_PATH
        $WP widget deactivate $($WP widget list sidebar-1 --fields=id --format=ids $WP_PATH) $WP_PATH
        # activate plugins
        $WP plugin activate autodescription $WP_PATH
        $WP plugin activate blackbar $WP_PATH
        $WP plugin activate imsanity $WP_PATH
        $WP plugin activate meta-box $WP_PATH
        $WP plugin activate pollux $WP_PATH
    fi
}

check_errors
get_config

echo "--------------------------------------------"
echo "Install WordPress                           "
echo "--------------------------------------------"

# Collect database variables
read -r -p "Enter the database name [${YELLOW}${DIR_NAME}${WHITE}]: " DBNAME
read -r -p "Enter the database user [${YELLOW}${DB_USER:-$DEFAULT_DB_USER}${WHITE}]: " DBUSER
read -r -p "Enter the database password [${YELLOW}${DB_PASS:-$DEFAULT_DB_PASS}${WHITE}]: " DBPASS
read -r -p "Enter the database prefix [${YELLOW}${DB_PREFIX:-$DEFAULT_DB_PREFIX}${WHITE}]: " DBPREFIX

# Collect wordpress credentials
read -r -p "Enter the administrator username [${YELLOW}${DEFAULT_WP_USER}${WHITE}]: " WPUSER
read -r -p "Enter the administrator password [${YELLOW}${DEFAULT_WP_PASS}${WHITE}]: " WPPASS
read -r -p "Enter the administrator email [${YELLOW}${DEFAULT_WP_EMAIL}${WHITE}]: " WPEMAIL

DBNAME=${DBNAME:-${DB_NAME:-$DIR_NAME}}
DBPASS=${DBPASS:-${DB_PASS:-$DEFAULT_DB_PASS}}
DBPREFIX=${DBPREFIX:-${DB_PREFIX:-$DEFAULT_DB_PREFIX}}
DBUSER=${DBUSER:-${DB_USER:-$DEFAULT_DB_USER}}
WPEMAIL=${WPEMAIL:-$DEFAULT_WP_EMAIL}
WPPASS=${WPPASS:-$DEFAULT_WP_PASS}
WPUSER=${WPUSER:-$DEFAULT_WP_USER}

set_config
install_wp
