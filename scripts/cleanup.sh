#!/bin/sh

DIR="$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" ; pwd)"

rm -rf "$DIR/.git"
git init

perl -ni -e 'print unless /install-wordpress.sh/' $DIR/composer.json
perl -ni -e 'print unless /install-castor.sh/' $DIR/composer.json
perl -ni -e 'print unless /cleanup.sh/' $DIR/composer.json
