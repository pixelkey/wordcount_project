function word_count() {
    $content = get_the_content();
    $stripped_content = strip_tags($content);
    $stripped_content = str_replace(array("\n", "\r", "\t"), ' ', $stripped_content);
    $word_count = str_word_count($stripped_content);
    return $word_count;
}
