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
    wp_add_inline_script('my-custom-js', 'var is_login = ' . is_user_logged_in() . ';', 'before'); 
   
    /** Let's say we want to include to only one specific page then how would that work */
    wp_enqueue_script('employee-search', plugin_dir_url(__FILE__) . 'js/employee-search.js', array('jquery'), $ver, true); 
    // Localize script to pass the AJAX URL to JavaScript
    wp_localize_script('employee-search', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    if (is_page('home')) {
        wp_enqueue_script('my-custom-js', $path_js, $dep, $ver, true); // true means add it in footer after body tag 
    }
    
}

add_action("wp_enqueue_scripts", 'my_custom_scripts');
/** Enqueue admin page related scripts */
add_action('admin_enqueue_scripts', 'enqueue_my_plugin_scripts');
add_action('wp_head','head_fun'); 
// Hook to add the menu and submenu
add_action('admin_menu', 'my_custom_plugin_menu');
// AJAX handler for searching employees
add_action('wp_ajax_search_employees', 'search_employees');
add_action('wp_ajax_nopriv_search_employees', 'search_employees');
add_shortcode('wp_sc_select', "shortcode_select");


function enqueue_my_plugin_scripts() { 
   
    $ver = filemtime(plugin_dir_path(__FILE__) . 'js/employee-search.js'); 
    wp_enqueue_script('jquery');
    wp_enqueue_script('employee-search', plugin_dir_url(__FILE__) . 'js/employee-search.js', array('jquery'), $ver, true); 
    // Localize script to pass the AJAX URL to JavaScript
    wp_localize_script('employee-search', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
} 
// ifyou want to enqueue scripts in admin panel then you can use admin enqueue scripts  
function shortcode_select()
{
    global $wpdb, $table_prefix;
    $wp_emp  = $table_prefix . 'emp';
    $q = "SELECT * FROM `$wp_emp`;";
    $results = $wpdb->get_results($q);
    ob_start();
?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($results as $row) :
            ?>
                <tr>
                    <td><?php echo $row->ID; ?></td>
                    <td><?php echo $row->name; ?></td>
                    <td><?php echo $row->email; ?></td>
                    <td><?php echo $row->status; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php
    $html = ob_get_clean();
    return $html;
} 
function my_posts()
{
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
            echo '<li><a href="'. get_the_permalink().'">' . esc_html(get_the_title()) . '</a></li>';
        }
        echo '</ul>';
    } else {
        esc_html_e('Sorry, no posts matched your criteria.');
    }
    // Restore original Post Data.
    wp_reset_postdata();
    $html = ob_get_clean();
    return $html;
}
add_shortcode('my-posts', "my_posts");
/**
 * This function is used in getting the number of times the post has been read.         
 */
function head_fun(){
    if(is_single()){
        // page , post,  attachment
        global $post;// this will have the post information which is opened
        echo $post->ID;
       $views =  get_post_meta($post->ID,'views',true);
        if($views != ""){
            $views++;
            update_post_meta($post->ID,'views',$views); 
        }else{
            add_post_meta($post->ID,'views',1); 
        }
        echo get_post_meta($post->ID,'views',true);
    }
} 
// Function to create the menu and submenu
function my_custom_plugin_menu() {
    // Add a top-level menu item
    add_menu_page(
        'My Plugin Settings',  // Page title
        'My Plugin',           // Menu title
        'manage_options',      // Capability required
        'my-plugin',           // Menu slug
        'my_plugin_main_page', // Callback function for the menu page
        'dashicons-admin-generic',  // Icon for the menu (dashicons)
        6                      // Position in the menu
    );
    
    // Add a submenu under the main menu
    add_submenu_page(
        'my-  ',           // Parent slug
        'Submenu 1',           // Page title
        'Submenu 1',           // Menu title
        'manage_options',      // Capability required
        'my-plugin-submenu-1', // Menu slug
        'my_plugin_submenu_1_page' // Callback function for the submenu page
    );

    add_submenu_page(
        'my-plugin',           // Parent slug
        'Submenu 2',           // Page title
        'Submenu 2',           // Menu title
        'manage_options',      // Capability required
        'my-plugin-submenu-2', // Menu slug
        'my_plugin_submenu_2_page' // Callback function for the submenu page
    );
} 
// Callback function for the main menu page
function my_plugin_main_page($data="") {
    include 'admin/main-page.php'; 
}
function my_plugin_main_page_fe() {
    $html = ' <div class="wrap">
  <h1>Employee List</h1>
  <input type="text" id="search_employee" placeholder="Search employee by name..." />
  <button id="search_button">Search</button>
  <div id="employee_list"></div>
</div>'; 
return $html;
}
add_shortcode('employee_data','my_plugin_main_page_fe');

// Callback function for the first submenu
function my_plugin_submenu_1_page() {
    ?>
    <div class="wrap">
        <h1>Submenu 1</h1>
        <p>This is the content of Submenu 1.</p>
    </div>
    <?php
} 
// Callback function for the second submenu
function my_plugin_submenu_2_page() {
    ?>
    <div class="wrap">
        <h1>Submenu 2</h1>
        <p>This is the content of Submenu 2.</p>
    </div>
    <?php
} 
function search_employees() {
  
    global $wpdb;
    $table_name = $wpdb->prefix . 'emp';
    $search = sanitize_text_field($_POST['search']);
   
    if (empty($search)) {
        $results = $wpdb->get_results("SELECT * FROM $table_name");
    } else {
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE name LIKE %s", '%' . $wpdb->esc_like($search) . '%'));
    }

    if ($results) {
        foreach ($results as $employee) {
            echo '<p>Name: ' . esc_html($employee->name) . ' | Email: ' . esc_html($employee->email) . ' | Phone: ' . esc_html($employee->phone) . '</p>';
        }
    } else {
        echo '<p>No employees found.</p>';
    } 
    wp_die();
}

/**
 * Custome post types
 */
// Hook into the 'init' action
add_action('init', 'create_custom_post_type'); 
// Function to create the custom post type
function create_custom_post_type() {
    $labels = array(
        'name'               => _x('Books name', 'post type general name'),
        'singular_name'      => _x('Book sn', 'post type singular name'),
        'menu_name'          => _x('Books mn', 'admin menu'),
        'name_admin_bar'     => _x('Book name admin', 'add new on admin bar'),
        'add_new'            => _x('Add New', 'book'),
        'add_new_item'       => __('Add New Book'),
        'new_item'           => __('New Book - new item'),
        'edit_item'          => __('Edit Book - edit item'),
        'view_item'          => __('View Book - view item'),
        'all_items'          => __('All Books - all items'),
        'search_items'       => __('Search Books - search items'),
        'parent_item_colon'  => __('Parent Books:'),
        'not_found'          => __('No books found.'),
        'not_found_in_trash' => __('No books found in Trash.'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'book'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
    );

    register_post_type('book', $args);
}