<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!function_exists('dwk_apm_add_admin_menu')) {
    function dwk_apm_add_admin_menu()
    {
        add_menu_page(
            'Payment Icons',
            'Payment Icons',
            'manage_options',
            'accepted-payment-methods',
            'dwk_apm_admin_page',
            'dashicons-money-alt'
        );
    }
}
add_action('admin_menu', 'dwk_apm_add_admin_menu');

if (!function_exists('dwk_apm_admin_page')) {
    function dwk_apm_admin_page()
    {
        $default_methods = [];
        $methods = get_option('dwk_apm_payment_methods', $default_methods);

        if (!is_array($methods)) {
            $methods = $default_methods;
        }
        $default_settings = array('alignment' => 'left', 'icon_size' => 50, 'tooltip' => 'yes', 'icon_spacing' => 10);
        $settings = get_option('dwk_apm_settings', $default_settings);

        $settings = wp_parse_args($settings, $default_settings);
        include(plugin_dir_path(__FILE__) . 'plugin-header.php');
?>
        <div class="wrap apm-admin-page">
            <h2 class="nav-tab-wrapper">
                <a href="#tab-1" class="nav-tab nav-tab-active"><?php esc_html_e('Accepted Payment Methods', 'accepted-payment-methods'); ?></a>
                <a href="#tab-2" class="nav-tab"><?php esc_html_e('Settings', 'accepted-payment-methods'); ?></a>
                <a href="#tab-3" class="nav-tab"><?php esc_html_e('Documentation', 'accepted-payment-methods'); ?></a>
            </h2>
            <div id="tab-1" class="tab-content">
                <div class="dwk-notice dwk-notice-inner">
                    <div class="dwk-notice__content">
                        <h3><?php esc_html_e('Add payment methods', 'accepted-payment-methods'); ?></h3>
                        <p><?php esc_html_e('Add the payment method icons/images and drag and drop the blocks for ordering the payment methods.', 'accepted-payment-methods'); ?></p>
                    </div>
                </div>
                <ul id="payment-methods-list">
                    <?php foreach ($methods as $method) : ?>
                        <?php if (is_array($method) && isset($method['name'], $method['icon'])) : ?>
                            <li class="payment-method-item" data-method="<?php echo esc_attr($method['name']); ?>">
                                <img src="<?php echo esc_url($method['icon']); ?>" alt="<?php echo esc_attr(ucfirst($method['name'])); ?>">
                                <span><?php echo esc_html(ucfirst(str_replace('-', ' ', $method['name']))); ?></span>
                                <button class="remove-method"><?php esc_html_e('Remove', 'accepted-payment-methods'); ?></button>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <form id="add-payment-method-form">
                    <label for="new-payment-method"><?php esc_html_e('New Payment Method:', 'accepted-payment-methods'); ?></label>
                    <input type="text" id="new-payment-method" name="new-payment-method" required>
                    <button id="upload-icon-button" class="button"><?php esc_html_e('Upload Icon', 'accepted-payment-methods'); ?></button>
                    <input type="hidden" id="upload-icon" name="upload-icon" required>
                    <button type="submit" id="add-payment-method-btn" class="button"><?php esc_html_e('Add Payment Method', 'accepted-payment-methods'); ?></button>
                </form>
                <button id="save-payment-methods" class="button button-primary"><?php esc_html_e('Save Changes', 'accepted-payment-methods'); ?></button>
                <p id="apm-save-message" class="hidden"></p>
            </div>
            <div id="tab-2" class="tab-content" style="display:none;">
                <div class="dwk-notice dwk-notice-inner">
                    <div class="dwk-notice__content">
                        <h3><?php esc_html_e('Settings', 'accepted-payment-methods'); ?></h3>
                        <p><?php esc_html_e('Experiment with the various settings available for the plugins to achieve optimal results.', 'accepted-payment-methods'); ?></p>
                    </div>
                </div>
                <form method="post" action="options.php">
                    <?php settings_fields('dwk_apm_settings_group'); ?>
                    <table class="form-table dwk-form-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Gap Alignment', 'accepted-payment-methods'); ?></th>
                            <td>
                                <select name="dwk_apm_settings[alignment]">
                                    <option value="left" <?php selected($settings['alignment'], 'left'); ?>><?php esc_html_e('Left', 'accepted-payment-methods'); ?></option>
                                    <option value="center" <?php selected($settings['alignment'], 'center'); ?>><?php esc_html_e('Center', 'accepted-payment-methods'); ?></option>
                                    <option value="right" <?php selected($settings['alignment'], 'right'); ?>><?php esc_html_e('Right', 'accepted-payment-methods'); ?></option>
                                </select>
                            </td>
                            <td><?php esc_html_e('Aligmnent of icons', 'accepted-payment-methods'); ?></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"> <?php esc_html_e('Size', 'accepted-payment-methods'); ?></th>
                            <td>
                                <input type="number" class="small-text" name="dwk_apm_settings[icon_size]" id="apm_icon_size" value="<?php echo esc_attr($settings['icon_size']); ?>">
                            </td>
                            <td>
                                <p class="description">
                                    <?php
                                    echo wp_kses_post(
                                        __('Size of Icons in px.', 'accepted-payment-methods')
                                    );
                                    ?>
                                </p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="dwk_apm_icon_spacing"><?php esc_html_e('Spacing', 'accepted-payment-methods'); ?></label></th>
                            <td>
                                <input type="number" class="small-text" name="dwk_apm_settings[icon_spacing]" id="dwk_apm_icon_spacing" value="<?php echo esc_attr($settings['icon_spacing']); ?>">
                            </td>
                            <td>
                                <p class="description"><?php esc_html_e('Gap between icons in px.', 'accepted-payment-methods'); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Display Tooltip.', 'accepted-payment-methods'); ?></th>
                            <td>
                                <select name="dwk_apm_settings[tooltip]">
                                    <option value="yes" <?php selected($settings['tooltip'], 'yes'); ?>>Yes</option>
                                    <option value="no" <?php selected($settings['tooltip'], 'no'); ?>>No</option>
                                </select>
                            </td>
                            <td><?php esc_html_e('Show the tooltip or not.', 'accepted-payment-methods'); ?></td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
            <div id="tab-3" class="tab-content" style="display:none;">
                <div class="dwk-notice dwk-notice-inner">
                    <div class="dwk-notice__content">
                        <h3><?php esc_html_e('Documentation', 'accepted-payment-methods'); ?></h3>
                        <p><?php esc_html_e('The basic guide on how to implement this plugin in your theme.', 'accepted-payment-methods'); ?></p>
                    </div>
                </div>
                <ol>
                    <li>
                        <p><strong><?php esc_html_e('Plugin shortcode: (Use this on a post or page)', 'accepted-payment-methods'); ?></strong></p>
                        <p><?php esc_html_e('Use the shortcode [dwk_apm_methods] to display them on your site', 'accepted-payment-methods'); ?></p>
                    </li>
                    <li>
                        <p><strong><?php esc_html_e('Template Tag: (Use this in a template php file', 'accepted-payment-methods'); ?></strong></p>
                        <pre><?php esc_html_e('<?php echo do_shortcode("[dwk_apm_methods]")?>', 'accepted-payment-methods'); ?></pre>
                    </li>
                </ol>

            </div>
        </div>
<?php
        include(plugin_dir_path(__FILE__) . 'plugin-footer.php');
    }
}
