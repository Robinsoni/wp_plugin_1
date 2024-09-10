<?php
 $args = array(
    'post_type' => 'post'
); 
$the_query = new WP_Query($args); 
// The Loop.
ob_start();
?><h1> Let's see all the posts</h1><?php
if ($the_query->have_posts()) {
    echo '<ul>';
    while ($the_query->have_posts()) {
        $the_query->the_post();
        echo '<li><a target="_blank" href="'. get_the_permalink().'">' . esc_html(get_the_title()) . '</a></li>';
    }
    echo '</ul>';
} else {
    esc_html_e('Sorry, no posts matched your criteria.');
}
// Restore original Post Data.
wp_reset_postdata();
$html = ob_get_clean();
echo $html;
?>