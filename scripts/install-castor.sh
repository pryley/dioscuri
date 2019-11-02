#!/bin/sh
# v2.0.0

DIR=${PWD}
DIR_NAME=${PWD##*/}

if [ "$DIR" == "scripts" ]; then
    DIR=$(cd ..;pwd)
    DIR_NAME=${DIR##*/}
fi

DIR_DEST=${DIR}/public/app/themes
DIR_THEME=${DIR}/theme
WHITE=`tput setaf 15`
WP=$DIR/vendor/bin/wp
WP_CORE_DIR=$DIR/public/wp
WP_PATH=--path=$WP_CORE_DIR
YELLOW=`tput setaf 3`

install_castor() {
    if [ ! -d $DIR_THEME ]; then
        git clone https://github.com/pryley/castor theme
        rm -rf "$DIR_THEME/.git"
        cd $DIR_THEME
        composer install
    fi
    perl -i -pe "s|castor.test|$DIR_NAME.test|g" $DIR_THEME/webpack.mix.js
    ln -fhs $DIR_THEME "$DIR_DEST/$THEME"
    cd ..
    $WP theme activate $THEME $WP_PATH
}

echo "--------------------------------------------"
echo "Install Castor                              "
echo "--------------------------------------------"

read -r -p "Enter the theme name [${YELLOW}${THEME_NAME:-$DIR_NAME}${WHITE}]: " THEME

THEME=${THEME:-${THEME_NAME:-$DIR_NAME}}

install_castor
