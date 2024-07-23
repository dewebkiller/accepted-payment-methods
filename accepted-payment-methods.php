<?php
/*
Plugin Name: Accepted Payment Methods
Plugin URI: http://example.com/
Description: Manage accepted payment methods with drag-and-drop sorting and customization.
Version: 1.0.0
Author: Niresh Shrestha
Author URI: http://example.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin paths
define( 'APM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'APM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include the admin page
require_once APM_PLUGIN_DIR . 'includes/admin-page.php';

// Enqueue admin scripts and styles
function apm_enqueue_admin_assets() {

    wp_enqueue_style( 'apm-admin-styles', APM_PLUGIN_URL . 'assets/css/admin-styles.css' );
    wp_enqueue_script( 'apm-admin-scripts', APM_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery', 'jquery-ui-sortable'), null, true );
    wp_localize_script( 'apm-admin-scripts', 'apmData', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce('your_nonce_action'),
        'plugin_url' => APM_PLUGIN_URL,
    ) );

    if ( is_admin() ) {
        wp_enqueue_media();
    }

}
add_action( 'admin_enqueue_scripts', 'apm_enqueue_admin_assets' );
// Enqueue frontend scripts and styles
function apm_enqueue_frontend_assets() {
    wp_enqueue_style( 'apm-frontend-styles', APM_PLUGIN_URL . 'assets/css/frontend-styles.css' );
    wp_enqueue_script( 'apm-frontend-scripts', APM_PLUGIN_URL . 'assets/js/frontend-script.js', array('jquery'), null, true );
}
add_action( 'wp_enqueue_scripts', 'apm_enqueue_frontend_assets' );

// Register plugin settings
function apm_register_settings() {
    register_setting( 'apm_settings_group', 'apm_settings' );
}
add_action( 'admin_init', 'apm_register_settings' );

// Handle AJAX requests for saving payment methods
function apm_save_payment_methods() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized user' );
    }
    if (isset($_POST['methods'])) {
      
        $methods = array_map(function($method, $image) {
            return [
                'name' => sanitize_text_field($method),
                'icon' => esc_url($image)
            ];
        }, array_keys($_POST['methods']), $_POST['methods']);
        
       
        update_option('apm_payment_methods', $methods);
        
       
        wp_send_json_success();
    } else {
        update_option('apm_payment_methods', '');
       
    }
}
add_action( 'wp_ajax_save_payment_methods', 'apm_save_payment_methods' );

// Handle AJAX requests for adding a payment method
function handle_add_payment_method() {
    // Verify nonce for security
    check_ajax_referer('your_nonce_action', 'nonce');

    // Get data from the request
    $method = sanitize_text_field($_POST['method']);
    $icon = $_POST['icon'];
    
    if (empty($method) || empty($icon)) {
        wp_send_json_error('Please enter payment method and upload an icon.');
        return;
    }


    wp_send_json_success();
}

add_action('wp_ajax_add_payment_method', 'handle_add_payment_method');
add_action('wp_ajax_nopriv_add_payment_method', 'handle_add_payment_method');