#!/bin/sh

WHITE=`tput setaf 15`
YELLOW=`tput setaf 3`

install_castor() {
	if [ ! -d $DIR_THEME ]; then
		git clone https://github.com/geminilabs/castor theme
		rm -rf "$DIR_THEME/.git"
		ln -fhs $DIR_THEME "$DIR_DEST/$THEME"
		cd $DIR_THEME
		composer install
		yarn
		gulp build
	fi
}

DIR="$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")"; pwd)"
DIR_THEME="$DIR/theme"
DIR_DEST="$DIR/public/app/themes"

echo "--------------------------------------------"
echo "Install Castor                              "
echo "--------------------------------------------"

read -r -p "Enter the theme name [${YELLOW}${THEME_NAME:-castor}${WHITE}]: " THEME

THEME=${THEME:-${THEME_NAME:-castor}}

install_castor
