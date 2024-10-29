<?php
/**
 * @package wordcounter
 * @version 0.0.1
 */
/*
Plugin Name: WordCounter
Plugin URI: http://google.com/
Description: This plugin counts the number of words in a post and displays it in a widget on the front end.
Author: Premanshu
Version: 0.0.1
Author URI: http://google.com/
*/

function examplefn()
{
    /** These are the lyrics to Hello Dolly */
    $lyrics = "Hello, Dolly
Well, hello, Dolly
It's so nice to have you back where you belong
You're lookin' swell, Dolly
I can tell, Dolly
You're still glowin', you're still crowin'
You're still goin' strong
I feel the room swayin'
While the band's playin'
One of our old favorite songs from way back when
So, take her wrap, fellas
Dolly, never go away again
Hello, Dolly
Well, hello, Dolly
It's so nice to have you back where you belong
You're lookin' swell, Dolly
I can tell, Dolly
You're still glowin', you're still crowin'
You're still goin' strong
I feel the room swayin'
While the band's playin'
One of our old favorite songs from way back when
So, golly, gee, fellas
Have a little faith in me, fellas
Dolly, never go away
Promise, you'll never go away
Dolly'll never go away again";

    // Here we split it into lines.
    $lyrics = explode("\n", $lyrics);

    // And then randomly choose a line.
    return wptexturize($lyrics[mt_rand(0, count($lyrics) - 1)]);
}

// This just echoes the chosen line, we'll position it later.
function example()
{
    $chosen = examplefn();
    $lang = '';
    if ('en_' !== substr(get_user_locale(), 0, 3)) {
        $lang = ' lang="en"';
    }

    printf(
        '<p id="dolly"><span class="screen-reader-text">%s </span><span dir="ltr"%s>%s</span></p>',
        __('Quote from Hello Dolly song, by Jerry Herman:'),
        $lang,
        $chosen
    );
}

// Now we set that function up to execute when the admin_notices action is called.
add_action('admin_notices', 'example');

// We need some CSS to position the paragraph.
function example_css()
{
    echo "
	<style type='text/css'>
	#dolly {
		float: right;
		padding: 5px 10px;
		margin: 0;
		font-size: 12px;
		line-height: 1.6666;
	}
	.rtl #dolly {
		float: left;
	}
	.block-editor-page #dolly {
		display: none;
	}
	@media screen and (max-width: 782px) {
		#dolly,
		.rtl #dolly {
			float: none;
			padding-left: 0;
			padding-right: 0;
		}
	}
	</style>
	";
}

add_action('admin_head', 'example_css');
