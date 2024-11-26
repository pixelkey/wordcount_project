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

/* Function to get the word count of the post */
function word_count()
{
	$content = get_the_content();
	$stripped_content = strip_tags($content);
	$stripped_content = str_replace(array("\n", "\r", "\t"), ' ', $stripped_content);
	$word_count = str_word_count($stripped_content);
	return $word_count;
}

/* Function to display the word count in the front end with custom CSS as per the user*/

function display_header($atts)
{

	$allowed_styles = array('bluesky', 'blackgrey', 'blackwhite', 'blackneon');

	if (isset($_POST['selected_style']) && in_array($_POST['selected_style'], $allowed_styles)) {
		$style = $_POST['selected_style'];
	} else {
		$style = 'bluesky';
	}

	// $atts = shortcode_atts(array(
	// 	'style' => 'bluesky',
	// ), $atts, 'word_counter');

	// $style = $atts['style'];

	if ($style == 'bluesky') {
		wp_enqueue_style('custom-counter-styles', plugin_dir_url(__FILE__) . 'public/css/bluesky.css');
	} elseif ($style == 'blackgrey') {
		wp_enqueue_style('custom-counter-styles2', plugin_dir_url(__FILE__) . 'public/css/blackgrey.css');
	} elseif ($style == 'blackwhite') {
		wp_enqueue_style('custom-counter-styles3', plugin_dir_url(__FILE__) . 'public/css/blackwhite.css');
	} elseif ($style == 'blackneon') {
		wp_enqueue_style('custom-counter-styles4', plugin_dir_url(__FILE__) . 'public/css/blackneon.css');
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

function wp_enqueue_styles9()
{
	wp_enqueue_style('button-styles', plugin_dir_url(__FILE__) . 'public/css/button.css');
}

add_action('wp_enqueue_scripts', 'wp_enqueue_styles9');

function select_style()
{
	$allowed_styles = array('bluesky', 'blackgrey', 'blackwhite', 'blackneon');

	if (isset($_POST['selected_style']) && in_array($_POST['selected_style'], $allowed_styles)) {
		$current_style = $_POST['selected_style'];
	} else {
		$current_style = 'bluesky';
	}

	$output = '<form method="post" class="style-selector-form">';
	$styles = array('bluesky', 'blackgrey', 'blackwhite', 'blackneon');
	foreach ($styles as $style) {
		$checked = ($current_style == $style) ? 'checked' : '';
		$style_name = ucfirst($style); // Capitalize the first letter
		$output .= '<div class="button"><label><input type="radio" name="selected_style" value="' . $style . '" ' . $checked . '> ' . $style_name . '</label><br></div>';
	}


	$output .= '</form>';
	$output .= '
    <script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        var radios = document.querySelectorAll(".style-selector-form input[name=\'selected_style\']");
        radios.forEach(function(radio) {
            radio.addEventListener("change", function() {
                this.form.submit();
            });
        });
    });
    </script>
    ';

	return $output;
}

add_shortcode('select_style', 'select_style');