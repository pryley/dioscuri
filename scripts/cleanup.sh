#!/bin/sh
# v2.0.0

DIR=${PWD}

if [ "$DIR" == "scripts" ]; then
    DIR=$(cd ..;pwd)
fi

rm -rf "$DIR/.git"
git init
