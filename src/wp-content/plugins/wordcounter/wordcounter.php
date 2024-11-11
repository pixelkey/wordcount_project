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

// function examplefn()
// {
// 	/** These are the lyrics to Hello Dolly */
// 	$lyrics = "Hello, Dolly
// Well, hello, Dolly
// It's so nice to have you back where you belong
// You're lookin' swell, Dolly
// I can tell, Dolly
// You're still glowin', you're still crowin'
// You're still goin' strong
// I feel the room swayin'
// While the band's playin'
// One of our old favorite songs from way back when
// So, take her wrap, fellas
// Dolly, never go away again
// Hello, Dolly
// Well, hello, Dolly
// It's so nice to have you back where you belong
// You're lookin' swell, Dolly
// I can tell, Dolly
// You're still glowin', you're still crowin'
// You're still goin' strong
// I feel the room swayin'
// While the band's playin'
// One of our old favorite songs from way back when
// So, golly, gee, fellas
// Have a little faith in me, fellas
// Dolly, never go away
// Promise, you'll never go away
// Dolly'll never go away again";

// 	// Here we split it into lines.
// 	$lyrics = explode("\n", $lyrics);

// 	// And then randomly choose a line.
// 	return wptexturize($lyrics[mt_rand(0, count($lyrics) - 1)]);
// }


function word_count()
{
	$content = get_the_content();
	$stripped_content = strip_tags($content);
	$stripped_content = str_replace(array("\n", "\r", "\t"), ' ', $stripped_content);
	$word_count = str_word_count($stripped_content);
	return $word_count;
}

// function enter_data($atts, $data = null)
// {
// 	// $data = $_GET("Enter the data");
// 	if (is_null($data)) {
// 		return "<p>Please enter the data</p>";
// 	}

// 	$data = sanitize_text_field($data);
// $words = str_word_count($data);
// $ans = "<p>Total word count is: $words</p>";
// return $data;
// }

function display_header($atts)
{
	$Art = get_the_content();
	$Art = strip_tags($Art);
	$count = str_word_count($Art);


	$counter = '
	<div class="wcount">Article word count is: <br><span id="counter" class= "wnumber">0</span></div>
    <script type="text/javascript">
        var counterElement = document.getElementById("counter");
        
        var maxCount = ' . $count . ';
		var startTime = null;
		var duration = 1000;

		 function getEasingFunction(maxCount) {
            if (maxCount <= 1) {
                return function(t) { return t; };
            } else {
                return function(t) { return t * t * t; };
            }
        }

        var easingFunction = getEasingFunction(maxCount);
		
		function animateCounter(timestamp){
		   if (!startTime) startTime = timestamp;
    		var progress = timestamp - startTime;
    		var t = Math.min(progress / duration, 1);
    		var easedPercentage = easingFunction(t);
    	
			var currentCount = Math.floor(easedPercentage * (maxCount - 1)) + 1;
			currentCount = Math.min(currentCount, maxCount);

    		counterElement.textContent = currentCount.toLocaleString();

	    if (progress < duration) {
    	    requestAnimationFrame(animateCounter);
    	} else {
        	counterElement.textContent = maxCount.toLocaleString();
    		}
		}
		requestAnimationFrame(animateCounter);

    </script>
    ';

	return $counter;

	//	var interval = Math.max(1, Math.floor(duration / maxCount));

	//	var currentCount = Math.floor(easedPercentage * maxCount);
	// var timer = setInterval(function() {
	// 	counter++;
	// 	counterElement.textContent = counter;
	// 	if (counter >= maxCount) {
	// 		clearInterval(timer);
	// 	}
	// }, interval);
	//return "<div class='wcount'>Article's Word count is: <div class='wnumber' span id='num'>$count</span></div></div>";
}


function count_style()
{
	echo "
	<style type='text/css'>
		.wcount {
			font-family: Georgia;
			font-size: 30px;
			text-align: center;
			border-style: out set;
			background-color: #f1f1f1;
			padding-top: 10px;
			margin-bottom: 0px;
			color: black;
		}
	</style>";
}

function number_style()
{
	echo "
	<style type='text/css'>
		.wnumber {
			font-family: Copperplate,fantasy;
			font-size: 70px;
			text-align: center;
			border-style: outset;
			background-color: #f1f1f1;
			padding-bottom: 10px;
			background-image: url('https://media.geeksforgeeks.org/wp-content/uploads/20231218222854/1.png');
			background-repeat: repeat;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;;
			text-transform: uppercase;
			color: white;

		}
	</style>";
}


add_action('wp_head', 'number_style');
add_action('wp_head', 'count_style');
add_shortcode('word_counter', 'display_header');

// add_action('wp_body_open', 'display_header', -1);
// add_shortcode('wordcounter', 'display_header');
// add_shortcode('data_entry', 'enter_data');
// function enter_data($atts, $content = null)
// {
// 	// Check if content is provided
// 	if (empty($content)) {
// 		return "<p>Please enter the data inside the shortcode.</p>";
// 	}

// 	// Sanitize the content
// 	$data = sanitize_text_field($content);

// 	// Count the words
// 	$words = str_word_count($data);

// 	// Prepare the output
// 	$ans = "<p class='wcount'>Total word count is: $words</p>";

// 	return $ans;
// }

// function count_style()
// {
// 	echo "
//     <style type='text/css'>
//         .wcount {
//             color: yellow;
//             background-color: black;
//             font-size: 30px;
//             padding: 10px;
//             text-align: center;
//         }
//     </style>";
// }

// // Hook the styles into wp_head
// add_action('wp_head', 'count_style');

// // Register the shortcode [word_counter]
// add_shortcode('word_counter', 'enter_data');