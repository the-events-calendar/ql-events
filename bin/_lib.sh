#!/usr/bin/env bash

set +u

install_wordpress() {
	if [ -f $WP_CORE_DIR/wp-config.php ]; then
		echo "Wordpress already installed."
		return;
	fi

	# Set the wordpress install directory and plugin paths in the composer.json
	composer config --unset extra.wordpress-install-dir;
	composer config extra.wordpress-install-dir $WP_CORE_DIR;

	composer config --unset extra.installer-paths;
	composer config --json extra.installer-paths "{
	\"$PLUGINS_DIR/{\$name}/\": [\"type:wordpress-plugin\"],
	\"$MUPLUGINS_DIR/{\$name}/\": [\"type:wordpress-muplugin\"],
	\"$THEMES_DIR/{\$name}/\": [\"type:wordpress-theme\"]
}"
	# Set WPackagist repository
	composer config repositories.wpackagist composer https://wpackagist.org

	# Enable plugins
	composer config --no-plugins allow-plugins.composer/installers true
	composer config --no-plugins allow-plugins.johnpbloch/wordpress-core-installer true

	# Install Wordpress + integrated plugins for testing/development.
	composer install --no-interaction
	composer require --no-interaction --dev -W \
		johnpbloch/wordpress:~${WP_VERSION} \
		wpackagist-plugin/the-events-calendar \
		wpackagist-plugin/event-tickets \
        wp-graphql/wp-graphql-jwt-authentication \
		wp-graphql/wp-graphql-woocommerce \
        wpackagist-plugin/woocommerce \
        wpackagist-plugin/woocommerce-gateway-stripe \
        wpackagist-plugin/wp-graphql \
        wpackagist-theme/twentytwentyone \
		wp-cli/wp-cli-bundle
}

remove_wordpress() {
	# Remove WordPress dependencies
	composer remove --dev wp-graphql/wp-graphql-jwt-authentication \
		wp-graphql/wp-graphql-woocommerce \
        wpackagist-plugin/woocommerce-gateway-stripe \
        wpackagist-plugin/wp-graphql \
        wpackagist-theme/twentytwentyone \
        wpackagist-plugin/woocommerce \
		wpackagist-plugin/the-events-calendar \
		wpackagist-plugin/event-tickets \
		johnpbloch/wordpress \
		composer/installers \
		wp-cli/wp-cli-bundle
}

install_local_test_library() {
	# Install testing library dependencies.
	composer require --no-interaction --dev --with-all-dependencies \
		lucatume/wp-browser \
		codeception/module-asserts:^1.0 \
		codeception/module-rest:^2.0 \
		codeception/util-universalframework:^1.0  \
		wp-graphql/wp-graphql-testcase:^2.3 \
        fzaninotto/faker:*

}

remove_local_composer_instance() {
	if [ -f $PROJECT_ROOT_DIR/vendor/bin/composer ]; then
		rm -f $PROJECT_ROOT_DIR/vendor/bin/composer
	else
		echo "No local composer instance found."
	fi
}

remove_project_symlink() {
	if [ -f $WP_CORE_DIR/wp-content/plugins/ql-events ]; then
		rm -rf $WP_CORE_DIR/wp-content/plugins/ql-events
		echo "Plugin symlink removed."
	else
		echo "Symlink no found."
	fi
}

remove_local_test_library() {
	# Remove testing library dependencies.
	echo "Removing testing libraries/packages..."
	composer remove --dev fzaninotto/faker \
		wp-graphql/wp-graphql-testcase \
		codeception/module-asserts \
		codeception/module-rest \
		codeception/util-universalframework \
		lucatume/wp-browser
	echo "Testing libraries removed!"
}

install_tec_extensions() {
	# Add extension Composer repositories
	echo "Setting TEC repositories..."
	composer config repositories.event_tickets_plus vcs git@github.com:the-events-calendar/event-tickets-plus.git
	composer config repositories.events_virtual vcs git@github.com:the-events-calendar/events-virtual.git
	composer config repositories.events_filterbar vcs git@github.com:the-events-calendar/events-filterbar.git
	composer config repositories.events_pro vcs git@github.com:the-events-calendar/events-pro.git
	echo "Repositories set!"

	echo "Installing TEC extensions..."
	composer require --no-interaction --dev \
		the-events-calendar/event-tickets-plus:dev-master \
		the-events-calendar/events-virtual:dev-master \
		the-events-calendar/events-filterbar:dev-master \
		the-events-calendar/events-pro:dev-master
	echo "TEC extensions installed!"
}

generate_tec_extension_loader_files() {
	echo "Generating TEC extension PSR file..."
	declare -a extensions=("event-tickets-plus" "events-virtual" "events-filterbar" "events-pro");
	for plugin in "${extensions[@]}"
	do
		:
		cd "${PROJECT_ROOT_DIR}/${PLUGINS_DIR}/${plugin}";
		#composer install --no-dev;
		composer dumpautoload -o;
	done
	echo "Generation done!"
}

remove_tec_extensions() {
	echo "Removing TEC extensions..."
	composer remove --dev \
		the-events-calendar/event-tickets-plus \
		the-events-calendar/events-virtual \
		the-events-calendar/events-filterbar \
		the-events-calendar/events-pro
	echo "TEC extensions removed!"
}

cleanup_composer_file() {
	echo "Removing extra config..."
	composer config --unset extra
	echo "Removing repositories..."
	composer config --unset repositories

	composer config --unset config.allow-plugins
	echo "composer.json cleaned!"
}

cleanup_local_files() {
	if [ -n "$(ls -A $WP_CORE_DIR)" ]; then
		echo "Removing final test files..."
		rm -rf $WP_CORE_DIR/*
		echo "Files removed!!"
	else
		echo "No files to remove!"
	fi

	echo "Rebuilding lock file..."
	rm -rf $PROJECT_ROOT_DIR/vendor
	composer install --no-dev
}

install_db() {
	if [ ${SKIP_DB_CREATE} = "true" ]; then
		echo "Skipping database creation..."
		return 0
	fi

	# parse DB_HOST for port or socket references
	local PARTS=(${DB_HOST//\:/ })
	local DB_HOSTNAME=${PARTS[0]};
	local DB_SOCK_OR_PORT=${PARTS[1]};
	local EXTRA=""

	if ! [ -z $DB_HOSTNAME ] ; then
		if [ $(echo $DB_SOCK_OR_PORT | grep -e '^[0-9]\{1,\}$') ]; then
			EXTRA=" --host=$DB_HOSTNAME --port=$DB_SOCK_OR_PORT --protocol=tcp"
		elif ! [ -z $DB_SOCK_OR_PORT ] ; then
			EXTRA=" --socket=$DB_SOCK_OR_PORT"
		elif ! [ -z $DB_HOSTNAME ] ; then
			EXTRA=" --host=$DB_HOSTNAME --protocol=tcp"
		fi
	fi

	# create database
	RESULT=`mysql -u $DB_USER --password="$DB_PASS" --skip-column-names -e "SHOW DATABASES LIKE '$DB_NAME'"$EXTRA`
	if [ "$RESULT" != $DB_NAME ]; then
			mysqladmin create $DB_NAME --user="$DB_USER" --password="$DB_PASS"$EXTRA
	fi
}


configure_wordpress() {
	if [ ${SKIP_WP_SETUP} = "true" ]; then
		echo "Skipping WordPress setup..."
		return 0
	fi

    cd $WP_CORE_DIR

	echo "Setting up WordPress..."
    wp config create --dbname="$DB_NAME" --dbuser="$DB_USER" --dbpass="$DB_PASS" --dbhost="$DB_HOST" --skip-check --force=true
    wp core install --url=wp.test --title="QL Events Tests" --admin_user=admin --admin_password=password --admin_email=admin@ql-event.local
    wp rewrite structure '/%year%/%monthnum%/%postname%/'
}

setup_plugin() {
	# Move to project root directory
	cd $PROJECT_ROOT_DIR

	# Add this repo as a plugin to the repo
	if [ ! -d $WP_CORE_DIR/wp-content/plugins/ql-events ]; then
		echo "Installing QL Events..."
		ln -s $PROJECT_ROOT_DIR $WP_CORE_DIR/wp-content/plugins/ql-events
	fi

	# Move to WordPress directory
	cd $WP_CORE_DIR

	if [ ${SKIP_WP_SETUP} = "true" ]; then
		echo "Skipping QL Events installation..."
		return 0
	fi

	# Activate the plugin, it's dependencies should be activated already.
	wp plugin activate ql-events

	# Flush the permalinks
	wp rewrite flush

	# Export the db for codeception to use
	if [ ! -d "$PROJECT_ROOT_DIR/local/db" ]; then
		mkdir ${PROJECT_ROOT_DIR}/local/db
	fi
	if [ -f "$PROJECT_ROOT_DIR/local/db/app_db.sql" ]; then
		echo "Deleting old DB dump..."
		rm -rf ${PROJECT_ROOT_DIR}/local/db/app_db.sql
	fi

	wp db export ${PROJECT_ROOT_DIR}/local/db/app_db.sql
}
