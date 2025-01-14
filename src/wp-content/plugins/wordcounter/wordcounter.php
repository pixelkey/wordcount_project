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
	wp_enqueue_scripts("customJS", get_template_directory_uri() . "public/js/wordcount.js", array('jquery'), 1.1, true);
	// wp_enqueue_script('bundle', plugin_dir_url() . '/js/bundle.js', ['jquery'], '1.0', true);
	return $word_count;
}
function display_header($atts)
{

	$allowed_styles = array('bluesky', 'blackgrey', 'blackwhite', 'blackneon');

	if (isset($_POST['selected_style']) && in_array($_POST['selected_style'], $allowed_styles)) {
		$style = $_POST['selected_style'];
	} else {
		$style = '';
	}

	wp_enqueue_style('custom-counter-styles', plugin_dir_url(__FILE__) . 'public/css/' . $style . '.css');

	$content = get_the_content();
	$stripped_content = strip_tags($content);
	$word_count = str_word_count($stripped_content);

	wp_enqueue_script('displaywordcount', plugin_dir_url(__FILE__) . 'public/js/displaywordcount.js', array(), '1.0', true);

	wp_localize_script('displaywordcount', 'counterData', array(
		'maxCount' => $word_count,
	));

	$counter = '
    <div class="card">
        <div class="bg">
            <div class="wcount">
                Word Count <br><hr>
                <span id="counter" class="wnumber">0</span>
            </div>
        </div>
        <div class="blob"></div>
    </div>
    ';
	return $counter;
}

add_shortcode('word_counter', 'display_header');
function select_style()
{
	$allowed_styles = array('bluesky', 'blackgrey', 'blackwhite', 'blackneon');

	$current_style = 'bluesky';
	wp_enqueue_script('style-selector', plugin_dir_url(__FILE__) . 'public/js/selectstyle.js', array(), '1.0', true);

	wp_enqueue_style('button-styles', plugin_dir_url(__FILE__) . 'assets/css/frontend/button.css');
	wp_localize_script('style-selector', 'styleSelectorData', array(
		'pluginBaseUrl' => plugin_dir_url(__FILE__),
	));

	$output = '<div id="style-selector-container"></div>';
	return $output;
}
add_shortcode('select_style', 'select_style');