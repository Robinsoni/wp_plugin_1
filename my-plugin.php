<?php
/*
Plugin Name: my plugin dev
Plugin URI:  
Description: Starter plugin development 
Author: Robin Soni
Version: 1.0
Requires at least: 5.9
Requires PHP: 7.4
Author URI: 
Text Domain: my-plugin
Domain Path: /languages/
 * */
if (!defined(('ABSPATH'))) {
    header("Location: /my_plugin");
    die("Can't access");
}

function my_plugin_activation()
{
    global $wpdb, $table_prefix;
    $wp_emp  = $table_prefix . 'emp';
    $q = "CREATE TABLE IF NOT EXISTS
    `$wp_emp` (
            `ID` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(50) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `status` BOOLEAN NOT NULL,
            PRIMARY KEY (`ID`)
        ) ENGINE = MyISAM; ";
    $wpdb->query($q);

    $data = array(
        'name' => 'Ak',
        'email' => 'ak@gmail.com',
        'status' => 1,
    );
    $wpdb->insert($wp_emp, $data);
}
function my_plugin_deactivation()
{
    global $wpdb, $table_prefix;
    $wp_emp = $table_prefix . 'emp';
    $q = "TRUNCATE `$wp_emp`";
    $wpdb->query($q);
}
register_activation_hook(
    __FILE__,
    'my_plugin_activation'
);

register_deactivation_hook(
    __FILE__,
    'my_plugin_deactivation'
);

function my_sc_fun($atts)
{ // for default attributes use shortcode atts
    $atts = array_change_key_case($atts, CASE_LOWER);
    $atts = shortcode_atts(array(
        'test' => "this is default value"
    ), $atts);

    /* 
    ob_start();
    // the html you write between this will be rendered
    return ob_get_clean();
     */
    include 'notice.php';
}
/* add_shortcode('my-sc', 'my_sc_fun'); */

function my_custom_scripts()
{

    $path_js = plugins_url('js/main.js', __FILE__);
    $path_style = plugins_url('css/style.css', __FILE__);

    $dep = array('jquery');
    $ver = filemtime(plugin_dir_path(__FILE__) . 'js/main.js'); // making it dynamic so that cache gets updated everytime the file is modified.
    $ver_style = filemtime(plugin_dir_path(__FILE__) . 'css/style.css'); // making it dynamic so that cache gets updated everytime the file is modified.
    // is_user_logged_in(); -  this helps you check whether the user is logged in or not
    wp_enqueue_style('my-custom-style', $path_style, "", $ver_style); // true means add it in footer after body tag
    wp_enqueue_script('my-custom-js', $path_js, $dep, $ver, true); // true means add it in footer after body tag
    wp_add_inline_script('my-custom-js','var is_login = '.is_user_logged_in().';','before'); 
    
    /** Let's say we want to include to only one specific page then how would that work */
    if(is_page('home')){
        wp_enqueue_script('my-custom-js', $path_js, $dep, $ver, true); // true means add it in footer after body tag 
    }

}
add_action("wp_enqueue_scripts", 'my_custom_scripts');

// ifyou want to enqueue scripts in admin panel then you can use admin enqueue scripts 
