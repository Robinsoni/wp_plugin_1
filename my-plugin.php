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
    $wpdb->insert($wp_emp,$data);
}
function my_plugin_deactivation()
{
    global $wpdb, $table_prefix;
    $wp_emp = $table_prefix.'emp';
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

function my_sc_fun()
{
    return 'Function Call';
}
add_shortcode('my-sc', 'my_sc_fun');
