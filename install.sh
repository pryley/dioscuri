#!/bin/sh
# v1.0.0

BOLD=`tput bold`
GREY=`tput setaf 8`
NORMAL=`tput sgr0`
RED=`tput setaf 9`
UNDERLINE=`tput smul`
WHITE=`tput setaf 15`

# Check that composer is installed
if ! which composer > /dev/null; then
    ERRORS=("${ERRORS[@]}" "==>${WHITE} composer ${GREY}${UNDERLINE}https://getcomposer.org/${NORMAL}")
fi

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

composer install

sh ./scripts/install-wordpress.sh
sh ./scripts/install-castor.sh
sh ./scripts/cleanup.sh

rm -rf ./scripts
rm ./install.sh
