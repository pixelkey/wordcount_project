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

	$atts = shortcode_atts(array(
		'style' => 'bluesky',
	), $atts, 'word_counter');

	$style = $atts['style'];

	if ($style == 'bluesky') {
		wp_enqueue_style('custom-counter-styles', plugin_dir_url(__FILE__) . 'public/css/bluesky.css');
	} elseif ($style == 'blackgrey') {
		wp_enqueue_style('custom-counter-styles2', plugin_dir_url(__FILE__) . 'public/css/blackgrey.css');
	} elseif ($style == 'blackwhite') {
		wp_enqueue_style('custom-counter-styles3', plugin_dir_url(__FILE__) . 'public/css/blackwhite.css');
	} else {
		wp_enqueue_style('custom-counter-styles', plugin_dir_url(__FILE__) . 'public/css/bluesky.css');
	}

	$Art = get_the_content();
	$Art = strip_tags($Art);
	$count = str_word_count($Art);


	$counter = '
	<div class="card"><div class="bg"><div class="wcount">Word Count <br><hr><span id="counter" class= "wnumber">0</span></div></div><div class="blob"></div></div>
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

