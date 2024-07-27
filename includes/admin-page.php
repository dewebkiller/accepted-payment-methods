<?php
function apm_add_admin_menu()
{
    add_menu_page(
        'Payment Icons',
        'Payment Icons',
        'manage_options',
        'accepted-payment-methods',
        'apm_admin_page',
        'dashicons-money-alt'
    );
}
add_action('admin_menu', 'apm_add_admin_menu');

function apm_admin_page()
{
    $default_methods = [];
    $methods = get_option('apm_payment_methods', $default_methods);

    if (!is_array($methods)) {
        $methods = $default_methods;
    }
    $default_settings = array('alignment' => 'left', 'icon_size' => 50, 'tooltip' => 'yes', 'icon_spacing' => 10);
    $settings = get_option('apm_settings', $default_settings);

    $settings = wp_parse_args($settings, $default_settings);
    include(plugin_dir_path(__FILE__) . 'plugin-header.php');
?>
    <div class="wrap apm-admin-page">
        <h2 class="nav-tab-wrapper">
            <a href="#tab-1" class="nav-tab nav-tab-active">Accepted Payment Methods</a>
            <a href="#tab-2" class="nav-tab">Settings</a>
            <a href="#tab-3" class="nav-tab">Documentation</a>
        </h2>
        <div id="tab-1" class="tab-content">
            <div class="dwk-notice dwk-notice-inner">
                <div class="dwk-notice__content">
                    <h3>Add payment methods</h3>
                    <p>Add the payment method icons/images and drag and drop the blocks for ordering the payment methods.</p>
                </div>
            </div>
            <ul id="payment-methods-list">
                <?php foreach ($methods as $method) : ?>
                    <?php if (is_array($method) && isset($method['name'], $method['icon'])) : ?>
                        <li class="payment-method-item" data-method="<?php echo esc_attr($method['name']); ?>">
                            <img src="<?php echo esc_url($method['icon']); ?>" alt="<?php echo esc_attr(ucfirst($method['name'])); ?>">
                            <span><?php echo ucfirst(str_replace('-', ' ', $method['name'])); ?></span>
                            <button class="remove-method">Remove</button>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <form id="add-payment-method-form">
                <label for="new-payment-method">New Payment Method:</label>
                <input type="text" id="new-payment-method" name="new-payment-method" required>
                <button id="upload-icon-button" class="button">Upload Icon</button>
                <input type="hidden" id="upload-icon" name="upload-icon" required>
                <button type="submit" id="add-payment-method-btn" class="button">Add Payment Method</button>
            </form>
            <button id="save-payment-methods" class="button button-primary">Save Changes</button>
            <p id="apm-save-message" class="hidden"></p>
        </div>
        <div id="tab-2" class="tab-content" style="display:none;">
            <div class="dwk-notice dwk-notice-inner">
                <div class="dwk-notice__content">
                    <h3>Settings</h3>
                    <p>Experiment with the various settings available for the plugins to achieve optimal results.</p>
                </div>
            </div>
            <form method="post" action="options.php">
                <?php settings_fields('apm_settings_group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Alignment</th>
                        <td>
                            <select name="apm_settings[alignment]">
                                <option value="left" <?php selected($settings['alignment'], 'left'); ?>>Left</option>
                                <option value="center" <?php selected($settings['alignment'], 'center'); ?>>Center</option>
                                <option value="right" <?php selected($settings['alignment'], 'right'); ?>>Right</option>
                            </select>
                        </td>
                        <td>Aligmnent of icons</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Size</th>
                        <td>
                        <input type="number" class="small-text" name="apm_settings[icon_size]" id="apm_icon_size" value="<?php echo esc_attr($settings['icon_size']); ?>">
                        </td>
                        <td>
                            <p class="description">
                                <?php
                                echo wp_kses_post(
                                    __('Size of Icons in px.', 'dwk-apm')
                                );
                                ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><label for="apm_icon_spacing"><?php esc_html_e('Spacing', 'dwk-apm'); ?></label></th>
                        <td>
                            <input type="number" class="small-text" name="apm_settings[icon_spacing]" id="apm_icon_spacing" value="<?php echo esc_attr($settings['icon_spacing']); ?>">
                        </td>
                        <td>
                            <p class="description"><?php esc_html_e('Gap between icons in px', 'dwk-apm'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Display Tooltip</th>
                        <td>
                            <select name="apm_settings[tooltip]">
                                <option value="yes" <?php selected($settings['tooltip'], 'yes'); ?>>Yes</option>
                                <option value="no" <?php selected($settings['tooltip'], 'no'); ?>>No</option>
                            </select>
                        </td>
                        <td>Show the tooltip or not.</td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <div id="tab-3" class="tab-content" style="display:none;">
            <div class="dwk-notice dwk-notice-inner">
                <div class="dwk-notice__content">
                    <h3>Documentation</h3>
                    <p>The basic guide on how to implement this plugin in your theme.</p>
                </div>
            </div>
            <ol>
                <li>
                    <p><strong>Plugin shortcode: (Use this on a post or page)</strong></p>
                    <p>Use the shortcode [dwk_apm_methods] to display them on your site</p>
                </li>
                <li>
                    <p><strong>Template Tag: (Use this in a template php file)</strong></p>
                    <pre>&lt;?php echo do_shortcode('[dwk_apm_methods]');?&gt;</pre>
                </li>
            </ol>

        </div>
    </div>
<?php
    include(plugin_dir_path(__FILE__) . 'plugin-footer.php');
}
