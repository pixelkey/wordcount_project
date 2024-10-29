#!/bin/bash

# Define Variables
# Determine the protocol and set SQUASH_URL based on $ENABLE_SSL
URL_PROTOCOL="https"
if [ "$SQUASH_DOMAIN" = "localhost.test" ] && [ "$ENABLE_SSL" = false ] ; then
    URL_PROTOCOL="http"
fi
SQUASH_URL="${URL_PROTOCOL}://${SQUASH_DOMAIN}"
echo "SQUASH_DOMAIN is set to: $SQUASH_DOMAIN"

# Check if WordPress is installed in a more concise way
IS_WORDPRESS_INSTALLED=false
if [ -e /var/www/html/index.php ] && [ -e /var/www/html/wp-includes/version.php ]; then
    IS_WORDPRESS_INSTALLED=true
fi


function create_wp_config() {
    # Create the wp-config.php file
    wp config create --dbname="${WORDPRESS_DB_NAME}" \
                     --dbuser="${WORDPRESS_DB_USER}" \
                     --dbpass="${WORDPRESS_DB_PASSWORD}" \
                     --dbhost="${WORDPRESS_DB_HOST}" \
                     --dbprefix="${WORDPRESS_TABLE_PREFIX}" \
                     --path="/var/www/html" \
                     --skip-check \
                     --allow-root \
                     --extra-php <<PHP
define( 'WP_HOME', '${SQUASH_URL}' );
define( 'WP_SITEURL', '${SQUASH_URL}' );
define( 'WP_CACHE', false );
PHP
}


# Function to change the ownership and permissions of the WordPress files
change_ownership_and_permissions() {
    # Change the ownership and permissions of the WordPress files
    chown -R "$1:$2" /var/www/html
    find /var/www/html -type d -exec chmod 755 {} \;
    find /var/www/html -type f -exec chmod 644 {} \;
}

# Wait for MySQL to be up
echo "Waiting for MySQL to be up..."
while ! nc -z db 3306; do sleep 1; done
echo "MySQL is up and running!"

# Use the LOCAL_UID and LOCAL_GID to set the user and group id as root
if [ "${LOCAL_UID:-33}" -ne 33 ] && [ "${LOCAL_GID:-33}" -ne 33 ]; then \
    usermod -u "${LOCAL_UID}" www-data && \
    groupmod -g "${LOCAL_GID}" www-data; \
fi

# Check if the flag file exists
if [ "$IS_WORDPRESS_INSTALLED" = false ]; then
    # Download WordPress core. Skip content download if wp-content exists
    wp core download --allow-root --path=/var/www/html --locale=en_AU --version="$WORDPRESS_VERSION" $( [ -d /var/www/html/wp-content ] && echo "--skip-content" )
    # Create the wp-config.php file
    create_wp_config
    # Copy the .htaccess file to the WordPress root directory
    cp -a /usr/local/bin/apache/.htaccess /var/www/html/.htaccess

    # Define source and destination directories
    SOURCE_DIR="/usr/local/bin/wp-content"
    DEST_DIR="/var/www/html/wp-content"

    # Check if the source directory exists and has zip files
    if [ -d "$SOURCE_DIR" ] && ls $SOURCE_DIR/*.zip 1> /dev/null 2>&1; then
        # Find and unzip all zip files to the destination directory
        find $SOURCE_DIR -name "*.zip" -exec unzip -o {} -d $DEST_DIR \;
    fi

    # Install pro plugins from the packages/plugins folder
    # Get all the zip files in the packages/plugins folder and install them
    if [ -d "/usr/local/bin/packages/plugins" ]; then
        # Get all the zip files in the packages/plugins folder and install them
        for file in /usr/local/bin/packages/plugins/*.zip; do
            wp plugin install $file --allow-root --activate --path=/var/www/html
        done
    fi

    # Set wordpress to discourage search engines from indexing this site
    wp option update blog_public 0 --allow-root --path=/var/www/html

    # Get the site url from the database
    OLD_SITE_URL=$(wp db query "SELECT option_value FROM wp_options WHERE option_name = 'siteurl'" --allow-root --path=/var/www/html --skip-column-names --silent)

    # Echo the OLD_SITE_URL
    echo "The OLD_SITE_URL is: $OLD_SITE_URL"

    # Replace the OLD_SITE_URL with the SQUASH_URL
    wp search-replace "${OLD_SITE_URL}" "${SQUASH_URL}" --allow-root --path=/var/www/html

    wp rewrite flush --allow-root --path=/var/www/html

    # Generate the .htaccess file with rewrite rules for pretty permalinks
    wp rewrite structure '/%postname%/' --hard --allow-root --path=/var/www/html
    wp rewrite flush --hard --allow-root --path=/var/www/html

    # # Install the desired plugins you want to manually include
    # # These are plugins that are not already in the packages/plugins folder nor in the database
    wp plugin install query-monitor --allow-root --activate --path=/var/www/html

    # Change the ownership and permissions of the WordPress files
    change_ownership_and_permissions "$LOCAL_UID" "$LOCAL_GID"


    # # Logs to Vector
    # curl -sSL https://logs.betterstack.com/setup-vector/ubuntu/tp9RJNLsUdiW74YTwfBxX8VL \
    #     -o /tmp/setup-vector.sh &&
    #     bash /tmp/setup-vector.sh

    # curl -X POST \
    #     -H 'Content-Type: application/json' \
    #     -H 'Authorization: Bearer tp9RJNLsUdiW74YTwfBxX8VL' \
    #     -d '{"dt":"'"$(date -u +'%Y-%m-%d %T UTC')"'","message":"Log stream started for '"$SQUASH_DOMAIN"'"}' \
    #     -k \
    #     https://in.logs.betterstack.com

    # Check if the database needs to be updated
    if wp core update-db --allow-root --path=/var/www/html; then
        echo "Database updated successfully"
    else
        echo "Failed to update the database"
    fi


    # Deactivate Wordfence
    wp plugin deactivate wordfence --allow-root --path=/var/www/html

    # Deactivate smtp plugin
    wp plugin deactivate wp-mail-smtp --allow-root --path=/var/www/html


    # IF CLEAN_DB is set to true, then run the following commands
    if [ "$CLEAN_DB" = true ] ; then
        # Since WordPress is installed, we can now run our custom SQL queries
        echo "Clearing WooCommerce orders, subscriptions, and customer data..."

        # Use environment variable for table prefix and substitute it in the SQL commands
        PREFIX=${WORDPRESS_TABLE_PREFIX}

        # Construct the SQL Queries with the interpolated table prefix
        SQL_QUERIES="
        DELETE FROM ${PREFIX}woocommerce_order_itemmeta;
        DELETE FROM ${PREFIX}woocommerce_order_items;
        DELETE FROM ${PREFIX}comments WHERE comment_type = 'order_note';
        DELETE FROM ${PREFIX}postmeta WHERE post_id IN ( SELECT ID FROM ${PREFIX}posts WHERE post_type = 'shop_order' );
        DELETE FROM ${PREFIX}posts WHERE post_type = 'shop_order';
        DELETE FROM ${PREFIX}posts WHERE post_type = 'shop_subscription';
        DELETE FROM ${PREFIX}users 
        WHERE ID NOT IN (
            SELECT DISTINCT post_author 
            FROM ${PREFIX}posts 
            WHERE post_type = 'post' 
            AND post_status = 'publish'
        ) 
        AND ( 
            ${PREFIX}users.ID IN (
                SELECT user_id 
                FROM ${PREFIX}usermeta 
                WHERE meta_key = '${PREFIX}capabilities' 
                AND meta_value LIKE '%customer%' 
                OR meta_value LIKE '%teacher%' 
                OR meta_value LIKE '%student%' 
                OR meta_value LIKE '%subscriber%'
            ) 
        );
        "

        # Run the SQL Queries using the WordPress CLI for the correct database credentials
        wp db query "$SQL_QUERIES" --allow-root --path=/var/www/html

        # Clean up all users with no posts published (excluding email addresses containing @pixelkey.com)
        SQL_CLEANUP_USERS="
        DELETE FROM ${PREFIX}users 
        WHERE ID NOT IN (
        SELECT DISTINCT post_author 
        FROM ${PREFIX}posts 
        WHERE post_type = 'post' 
        AND post_status = 'publish'
        ) 
        AND user_email NOT LIKE '%@pixelkey.com';

        -- Also consider deleting old user meta
        DELETE FROM ${PREFIX}usermeta WHERE user_id NOT IN (SELECT ID FROM ${PREFIX}users);
        "

        # Run the User Cleanup SQL Queries using the WordPress CLI
        wp db query "$SQL_CLEANUP_USERS" --allow-root --path=/var/www/html

        # New SQL section to delete all Action Scheduler actions
        SQL_DELETE_ACTIONS="
        DELETE FROM ${PREFIX}actionscheduler_actions;
        "

        # Run the Delete Actions SQL Query
        wp db query "$SQL_DELETE_ACTIONS" --allow-root --path=/var/www/html

        # # New SQL section to reduce the number of old posts
        # SQL_REDUCE_POSTS="
        # DELETE FROM ${PREFIX}posts WHERE post_type = 'post' AND post_date < '2021-01-01 00:00:00';
        # "

        # # Run the Reduce Posts SQL Query
        # wp db query "$SQL_REDUCE_POSTS" --allow-root --path=/var/www/html

        # # New SQL section to delete all comments
        # SQL_DELETE_COMMENTS="
        # DELETE FROM ${PREFIX}comments;
        # "

        # # Run the Delete Comments SQL Query
        # wp db query "$SQL_DELETE_COMMENTS" --allow-root --path=/var/www/html

        # # New SQL section to create a new admin user
        # SQL_CREATE_ADMIN="
        # INSERT INTO ${PREFIX}users (user_login, user_pass, user_nicename, user_email, user_status, display_name)
        # VALUES ('pixelkeysupport', MD5('Pixelkey@Support'), 'PixelKey Support', 'support@pixelkey.com', 0, 'PixelKey Support');
        # SET @user_id = LAST_INSERT_ID();
        # INSERT INTO ${PREFIX}usermeta (user_id, meta_key, meta_value)
        # VALUES (@user_id, '${PREFIX}capabilities', 'a:1:{s:13:\"administrator\";b:1;}');
        # INSERT INTO ${PREFIX}usermeta (user_id, meta_key, meta_value)
        # VALUES (@user_id, '${PREFIX}user_level', '10');
        # "

        # # Run the Create Admin User SQL Query
        # wp db query "$SQL_CREATE_ADMIN" --allow-root --path=/var/www/html


        # Path where the db dump will be stored within the container.
        DUMP_FILE="/usr/local/bin/db.sql.gz" # Matches the mounted volume path in docker-compose.yml

        # Dump and compress the WordPress database.
        echo "Dumping the WordPress database to ${DUMP_FILE}..."
        wp db export - --allow-root --path=/var/www/html | gzip > $DUMP_FILE

        # Check for successful completion of mysqldump
        if [ $? -eq 0 ]; then
        echo "Database dumped to ${DUMP_FILE} and compressed successfully."
        else
        echo "Failed to dump the database."
        fi
    fi

    # Delete the packages and wp-content directories from bin
    rm -rf /usr/local/bin/packages
    rm -rf /usr/local/bin/wp-content

fi

# If w-config.php does not exist, create it. This may be the case if we have manually extracted the WordPress files from production
if [ ! -e /var/www/html/wp-config.php ]; then
    create_wp_config
fi

# Finally, confirm file ownership and permissions
change_ownership_and_permissions "$LOCAL_UID" "$LOCAL_GID"

# Wait for the background WordPress process
wait $!

# Hand over to the Apache process
exec apache2-foreground