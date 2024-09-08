<?php 
if(!defined('WP_UNINSTALL_PLUGIN')){
    header("Location: /test");
    die(" uninstallation");
}

global $wpdb, $table_prefix;
$wp_emp = $table_prefix.'emp';
$q = "DROP TABLE `$wp_emp`;";
$wpdb->query();