#!/usr/bin/env bash

set +u

if [[ -z "$WP_TEST_DB_NAME" ]]; then
	echo "WP_TEST_DB_NAME not found"
	print_usage_instruction
fi
DB_NAME="$WP_TEST_DB_NAME"
if [[ -z "$WP_TEST_DB_USER" ]]; then
	echo "WP_TEST_DB_USER not found"
	print_usage_instruction
fi
DB_USER="$WP_TEST_DB_USER"

DB_HOST=${WP_TEST_DB_HOST-localhost}
DB_PASS=${WP_TEST_DB_PASSWORD-""}
WP_VERSION=${WP_VERSION-6}
PROJECT_ROOT_DIR=$(pwd)
WP_CORE_DIR=${WP_CORE_DIR:-local/public}
PLUGINS_DIR=${PLUGINS_DIR:-"$WP_CORE_DIR/wp-content/plugins"}
MUPLUGINS_DIR=${MUPLUGINS_DIR:-"$WP_CORE_DIR/wp-content/mu-plugins"}
THEMES_DIR=${THEMES_DIR:-"$WP_CORE_DIR/wp-content/themes"}
SKIP_DB_CREATE=${SKIP_DB_CREATE-false}
COMPOSER_TOKEN=${COMPOSER_TOKEN}
COMPOSER_AUTH=${COMPOSER_AUTH:-"{\"github-oauth\": {\"github.com\": \"$COMPOSER_TOKEN\"} }"}
