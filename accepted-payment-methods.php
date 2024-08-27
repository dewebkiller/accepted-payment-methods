<?php
/*
Plugin Name: Accepted Payment Methods
Plugin URI: https://www.niresh.com.np/
Description: Manage accepted payment methods with drag-and-drop sorting and customization.
Version: 1.2
Author: Niresh Shrestha
Text Domain: accepted-payment-methods
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin paths
define('DWKAPM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DWKAPM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the admin page
require_once DWKAPM_PLUGIN_DIR . 'includes/admin-page.php';
require_once DWKAPM_PLUGIN_DIR . 'includes/frontend-page.php';

// Enqueue admin scripts and styles
if (!function_exists('dwk_apm_enqueue_admin_assets')) {
    function dwk_apm_enqueue_admin_assets()
    {

        wp_enqueue_style('apm-admin-styles', DWKAPM_PLUGIN_URL . 'assets/css/admin-styles.css', array(), '1.0');
        wp_enqueue_script('apm-admin-scripts', DWKAPM_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery', 'jquery-ui-sortable'), '1.0', true);
        wp_localize_script('apm-admin-scripts', 'apmData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('save_payment_methods_action'),
            'plugin_url' => DWKAPM_PLUGIN_URL,
        ));

        if (is_admin()) {
            wp_enqueue_media();
        }
    }
}
add_action('admin_enqueue_scripts', 'dwk_apm_enqueue_admin_assets');
// Enqueue frontend scripts and styles
if (!function_exists('dwk_apm_enqueue_frontend_assets')) {
    function dwk_apm_enqueue_frontend_assets()
    {
        wp_enqueue_style('apm-frontend-styles', DWKAPM_PLUGIN_URL . 'assets/css/frontend-styles.css', array(), '1.0');
        wp_enqueue_script('apm-frontend-scripts', DWKAPM_PLUGIN_URL . 'assets/js/frontend-script.js', array('jquery'), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'dwk_apm_enqueue_frontend_assets');

// Register plugin settings


if (!function_exists('dwk_apm_register_settings')) {
    function dwk_apm_register_settings()
    {
        register_setting('dwk_apm_settings_group', 'dwk_apm_settings');
    }
}
add_action('admin_init', 'dwk_apm_register_settings');

// Handle AJAX requests for saving payment methods
if (!function_exists('dwk_apm_save_payment_methods')) {
    function dwk_apm_save_payment_methods()
    {
        if (! current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized user');
        }
        // Verify the nonce
        check_ajax_referer('save_payment_methods_action', 'nonce');
        if (isset($_POST['methods'])) {

            // Initialize an empty array to store sanitized methods
            $methods = [];

            // Check if 'methods' is set in $_POST and is an array
            if (isset($_POST['methods']) && is_array($_POST['methods'])) {
                foreach ($_POST['methods'] as $method => $image) {
                    // Sanitize the method name and the image URL
                    $sanitized_method = sanitize_text_field($method);
                    $sanitized_image = esc_url_raw($image);

                    // Add the sanitized data to the $methods array
                    $methods[] = [
                        'name' => $sanitized_method,
                        'icon' => $sanitized_image
                    ];
                }
            }


            update_option('dwk_apm_payment_methods', $methods);


            wp_send_json_success();
        } else {
            update_option('dwk_apm_payment_methods', '');
        }
    }
}
add_action('wp_ajax_save_payment_methods', 'dwk_apm_save_payment_methods');

// Handle AJAX requests for adding a payment method
if (!function_exists('dwk_apm_add_payment_method')) {
    function dwk_apm_add_payment_method()
    {
        // Verify nonce for security
        check_ajax_referer('save_payment_methods_action', 'nonce');

        // Get data from the request
        $method = sanitize_text_field($_POST['method']);
        //$icon = $_POST['icon'];
        $icon = isset($_POST['icon']) ? sanitize_text_field($_POST['icon']) : '';

        if (empty($method) || empty($icon)) {
            wp_send_json_error('Please enter payment method and upload an icon.');
            return;
        }


        wp_send_json_success();
    }
}
add_action('wp_ajax_add_payment_method', 'dwk_apm_add_payment_method');
add_action('wp_ajax_nopriv_add_payment_method', 'dwk_apm_add_payment_method');
