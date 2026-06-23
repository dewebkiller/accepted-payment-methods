<?php
/*
Plugin Name: Accepted Payment Methods
Plugin URI: https://www.niresh.com.np/
Description: Manage accepted payment methods with drag-and-drop sorting and customization.
Version: 1.5.0
Author: Niresh Shrestha
Text Domain: accepted-payment-methods
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
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
    function dwk_apm_enqueue_admin_assets($hook)
    {
        if ('toplevel_page_accepted-payment-methods' !== $hook) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('apm-admin-styles', DWKAPM_PLUGIN_URL . 'assets/css/admin-styles.css', array(), '1.0');
        wp_enqueue_script('apm-admin-scripts', DWKAPM_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery', 'jquery-ui-sortable'), '1.0', true);
        wp_localize_script('apm-admin-scripts', 'apmData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('save_payment_methods_action'),
            'plugin_url' => DWKAPM_PLUGIN_URL,
            'msg_empty_method' => __('Please enter payment method or upload an icon.', 'accepted-payment-methods'),
            'msg_upload_icon' => __('Please upload an icon for the payment method.', 'accepted-payment-methods'),
            'msg_error_add' => __('Error adding payment method.', 'accepted-payment-methods'),
            'msg_save_success' => __('Payment methods saved successfully!', 'accepted-payment-methods'),
            'msg_save_error' => __('Error saving payment methods.', 'accepted-payment-methods'),
        ));
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


if (!function_exists('dwk_apm_sanitize_settings')) {
    function dwk_apm_sanitize_settings($input)
    {
        $sanitized = [];
        if (isset($input['alignment'])) {
            $sanitized['alignment'] = in_array($input['alignment'], ['left', 'center', 'right'], true) ? $input['alignment'] : 'left';
        }
        if (isset($input['icon_size'])) {
            $sanitized['icon_size'] = absint($input['icon_size']);
        }
        if (isset($input['icon_spacing'])) {
            $sanitized['icon_spacing'] = absint($input['icon_spacing']);
        }
        if (isset($input['tooltip'])) {
            $sanitized['tooltip'] = in_array($input['tooltip'], ['yes', 'no'], true) ? $input['tooltip'] : 'yes';
        }
        return $sanitized;
    }
}

if (!function_exists('dwk_apm_register_settings')) {
    function dwk_apm_register_settings()
    {
        register_setting('dwk_apm_settings_group', 'dwk_apm_settings', [
            'sanitize_callback' => 'dwk_apm_sanitize_settings'
        ]);
    }
}
add_action('admin_init', 'dwk_apm_register_settings');

// Handle AJAX requests for saving payment methods
if (!function_exists('dwk_apm_save_payment_methods')) {
    function dwk_apm_save_payment_methods()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized user');
        }
        // Verify the nonce
        check_ajax_referer('save_payment_methods_action', 'nonce');

        $source = isset($_POST['source']) ? sanitize_text_field($_POST['source']) : 'manual';
        update_option('dwk_apm_method_source', $source);

        if ($source === 'manual') {
            $methods = [];
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
        } else {
            $checked_library = [];
            if (isset($_POST['checked_library']) && is_array($_POST['checked_library'])) {
                foreach ($_POST['checked_library'] as $item) {
                    $checked_library[] = sanitize_text_field($item);
                }
            }
            update_option('dwk_apm_checked_library_methods', $checked_library);
        }

        wp_send_json_success();
    }
}
add_action('wp_ajax_save_payment_methods', 'dwk_apm_save_payment_methods');

// Handle AJAX requests for adding a payment method
if (!function_exists('dwk_apm_add_payment_method')) {
    function dwk_apm_add_payment_method()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized user');
        }
        // Verify nonce for security
        check_ajax_referer('save_payment_methods_action', 'nonce');

        // Get data from the request
        $method = sanitize_text_field($_POST['method']);
        $icon = isset($_POST['icon']) ? sanitize_text_field($_POST['icon']) : '';

        if (empty($method) || empty($icon)) {
            wp_send_json_error('Please enter payment method and upload an icon.');
            return;
        }


        wp_send_json_success();
    }
}
add_action('wp_ajax_add_payment_method', 'dwk_apm_add_payment_method');

// Retrieve all available pre-built payment methods dynamically by scanning the assets/icons/ directory
if (!function_exists('dwk_apm_get_prebuilt_payment_methods')) {
    function dwk_apm_get_prebuilt_payment_methods()
    {
        $dir = DWKAPM_PLUGIN_DIR . 'assets/icons/';
        $methods = [];

        if (is_dir($dir)) {
            // Find all files with png, jpg, jpeg, svg, webp extensions
            $files = glob($dir . '*.{png,jpg,jpeg,svg,webp}', GLOB_BRACE);
            if (is_array($files)) {
                foreach ($files as $file) {
                    $filename = basename($file);
                    // Exclude logo.png if it's somehow still in assets/icons
                    if ($filename === 'logo.png') {
                        continue;
                    }

                    $slug = pathinfo($filename, PATHINFO_FILENAME);
                    
                    // Generate a human-readable label from the slug
                    $label = ucfirst(str_replace('-', ' ', $slug));
                    
                    // Specific overrides for standard names with translation strings
                    if ($slug === 'applepay') {
                        $label = __('Apple Pay', 'accepted-payment-methods');
                    } elseif ($slug === 'googlepay') {
                        $label = __('Google Pay', 'accepted-payment-methods');
                    } elseif ($slug === 'paypal') {
                        $label = __('PayPal', 'accepted-payment-methods');
                    } elseif ($slug === 'mastercard') {
                        $label = __('Mastercard', 'accepted-payment-methods');
                    } elseif ($slug === 'visa') {
                        $label = __('Visa', 'accepted-payment-methods');
                    } elseif ($slug === 'amazon') {
                        $label = __('Amazon Pay', 'accepted-payment-methods');
                    } elseif ($slug === 'bitcoin') {
                        $label = __('Bitcoin', 'accepted-payment-methods');
                    }

                    $methods[$slug] = [
                        'label' => $label,
                        'file'  => $filename,
                    ];
                }
            }
        }

        // Sort dynamically fetched options alphabetically by label
        uasort($methods, function($a, $b) {
            return strcasecmp($a['label'], $b['label']);
        });

        return $methods;
    }
}
