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

function enqueue_custom_counter_styles()
{
	wp_enqueue_style('custom-counter-styles', plugin_dir_url(__FILE__) . 'public/css/blackgrey.css');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_counter_styles');


function word_count()
{
	$content = get_the_content();
	$stripped_content = strip_tags($content);
	$stripped_content = str_replace(array("\n", "\r", "\t"), ' ', $stripped_content);
	$word_count = str_word_count($stripped_content);
	return $word_count;
}



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
}

add_shortcode('word_counter', 'display_header');

