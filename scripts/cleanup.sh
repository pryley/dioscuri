#!/bin/sh
# v2.0.0

DIR="$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" ; pwd)"

rm -rf "$DIR/.git"
git init
