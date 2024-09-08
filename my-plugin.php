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
if(!defined(('ABSPATH'))){
    header("Location: /my_plugin");
    die("Can't access");
}

function my_plugin_activation(){
   
}
function my_plugin_deactivation(){

}
register_activation_hook(
	__FILE__,
	'my_plugin_activation'
);

register_deactivation_hook(
	__FILE__,
	'my_plugin_deactivation'
);

function my_sc_fun(){
    return 'Function Call';
}
add_shortcode('my-sc','my_sc_fun');