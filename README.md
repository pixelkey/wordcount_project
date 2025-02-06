Description

Wordcount Plugin is a simple WordPress plugin that automatically counts and displays the word count of a page on the right side of the page. It dynamically counts from 0 to the total word count when the page loads. The plugin also provides four different display styles to choose from.

Features

- Automatically counts words in posts and pages

- Displays word count dynamically from 0 to the total count

- Shows the word count on the right side of the page

- Offers four different styles for customization

- Lightweight and easy to use

Installation

Method 1: Install from GitHub

Download the plugin:
```shell
git clone https://github.com/your-username/wordcount-plugin.git
```
Move the plugin to your WordPress installation:
```shell
mv wordcount-plugin /path-to-your-local-wordpress/wp-content/plugins/
```
Activate the plugin:

- Log in to WordPress Admin

- Go to Plugins → Installed Plugins

- Find Wordcount Plugin and click Activate

Method 2: Upload via WordPress Dashboard

1. Download the ZIP file from GitHub

2. Go to WordPress Admin → Plugins → Add New → Upload Plugin

3. Select the ZIP file and click "Install Now"

4. Click "Activate" after installation

How to Use

1. After activation, the plugin will automatically count and display the word count on the right side of each page.

2. The word count starts from 0 and animates to the total count when the page loads.

3. You can customize the appearance using the four available styles in the Wordcount Plugin Settings.

Updating the Plugin

To update manually, run:
```shell
cd /path-to-your-local-wordpress/wp-content/plugins/wordcount-plugin
git pull origin main
```
Then refresh your WordPress admin panel.

Troubleshooting

Enable Debugging

If you have issues, enable debugging in ` wp-config.php `:

```shell
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check logs in ` /wp-content/debug.log. `

Common Issues & Fixes

- Word count not updating? → Clear your browser cache and refresh.

- Plugin not appearing? → Ensure it is activated.

- Conflicts with other plugins? → Try disabling other plugins to check for conflicts.

- Word count animation not working? → Make sure JavaScript is enabled and no script errors exist.