<?php
/**
 * @package wordcounter
 * @version 0.0.1
 */
/*
Plugin Name: Word Counter
Plugin URI: http://google.com
Description: This is a plugin that counts the number of words in a post and display in the front end.
Author: Premanshu
Version: 0.0.1
Author URI: http://google.com
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


function word_count()
{
	$content = get_the_content();
	$stripped_content = strip_tags($content);
	$stripped_content = str_replace(array("\n", "\r", "\t"), ' ', $stripped_content);
	$word_count = str_word_count($stripped_content);
	return $word_count;
}

function display_header()
{
	$count = word_count();
	printf('<h1 class = "wcount">Total Word count is: %d</h1>', $count);
}

function count_style()
{
	echo "
	<style type='text/css'>
		.wcount {
			color: yellow;
			background-color: Black;
			text-align: center;
			font-size: 16px;
		}
	</style>";
}

add_action('wp_head', 'count_style');
add_action('wp_body_open', 'display_header', -1);
