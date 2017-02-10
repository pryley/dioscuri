#!/bin/sh

BOLD=`tput bold`
UNDERLINE=`tput smul`
NORMAL=`tput sgr0`
WHITE=`tput setaf 15`
GREY=`tput setaf 8`
RED=`tput setaf 9`
GREEN=`tput setaf 10`

check_errors() {
	ERRORS=()
	# Check that mysql is installed
	if ! which mysql > /dev/null; then
		ERRORS=("${ERRORS[@]}" "==>${WHITE} mysql ${GREY}${UNDERLINE}https://dev.mysql.com/downloads/mysql/${NORMAL}")
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
	DEV_ENV=$(perl -lne 'm{development.*?([\w.-:\/]+)} and print $1' $ENV_FILE)
}

set_config() {
	# perl -i -wne 'print unless /(AUTH_KEY|SECURE_AUTH_KEY|LOGGED_IN_KEY|NONCE_KEY|AUTH_SALT|SECURE_AUTH_SALT|LOGGED_IN_SALT|NONCE_SALT)/' $ENV_FILE
	perl -i -pwe "/DB_NAME/ && s|'${DB_NAME}'|'${DBNAME}'|" $ENV_FILE
	perl -i -pwe "/DB_USER/ && s|'${DB_USER}'|'${DBUSER}'|" $ENV_FILE
	perl -i -pwe "/DB_PASS/ && s|'${DB_PASS}'|'${DBPASS}'|" $ENV_FILE
	perl -i -pwe "/development/ && s|'$DEV_ENV'|'http://$DIR_NAME.dev'|" $ENV_FILE
	perl -i -pe 'BEGIN {
		@chars = ("a" .. "z", "A" .. "Z", 0 .. 9);
		push @chars, split //, "!@#$%^&*()-_ []{}<>~\`+=,.;:/?|";
		sub salt { join "", map $chars[ rand @chars ], 1 .. 64 }
	}
	s/put your unique phrase here/salt()/ge' $ENV_FILE
}

install_wp() {
	wp db create $WP_PATH > /dev/null 2>&1
	if ! wp core is-installed $WP_PATH; then
		wp core install $WP_PATH --url="http://${DIR_NAME}.dev" --title="${DIR_NAME}" --admin_user="dev" --admin_password="dev" --admin_email="dev@${DIR_NAME}.dev"
		# set options
		wp option update welcome 0 $WP_PATH
		wp option update uploads_use_yearmonth_folders 0 $WP_PATH
		wp option update blogdescription "" $WP_PATH
		wp option update permalink_structure "/%postname%/" $WP_PATH
		wp option update show_on_front page $WP_PATH
		wp option update page_on_front 2 $WP_PATH
		wp post delete 1 --force $WP_PATH
		wp post update 2 --post_title=Home --post_name=home --post_content= $WP_PATH
		wp user meta update 1 show_welcome_panel 0 $WP_PATH
		wp widget deactivate $(wp widget list sidebar-1 --fields=id --format=ids $WP_PATH) $WP_PATH
		# activate plugins
		wp plugin activate blackbox $WP_PATH
		wp plugin activate imsanity $WP_PATH
		wp plugin activate meta-box $WP_PATH
		wp plugin activate pollux $WP_PATH
		wp plugin activate post-type-archive-links $WP_PATH
		wp plugin activate simple-custom-post-order $WP_PATH
	fi
}

check_errors

DIR="$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" ; pwd)"
DIR_NAME="$(basename "${DIR}")"
ENV_FILE=$DIR/env.php
WP_CORE_DIR="$DIR/public/wp"
WP_PATH='--path=public/wp'

get_config

echo "--------------------------------------------"
echo "Install WordPress                           "
echo "--------------------------------------------"

# Collect database variables
read -r -p "Enter the database name [${yellow}${DB_NAME:-$DIR_NAME}${white}]: " DBNAME
read -r -p "Enter the database user [${yellow}${DB_USER:-dev}${white}]: " DBUSER
read -r -p "Enter the database password [${yellow}${DB_PASS:-dev}${white}]: " DBPASS

DBNAME=${DBNAME:-${DB_NAME:-$DIR_NAME}}
DBUSER=${DBUSER:-${DB_USER:-dev}}
DBPASS=${DBPASS:-${DB_PASS:-dev}}

set_config
install_wp
