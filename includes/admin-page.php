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
        $source = get_option('dwk_apm_method_source', 'manual');
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
                        <p><?php esc_html_e('Choose how you want to manage your accepted payment methods: upload manually or select from the pre-built library.', 'accepted-payment-methods'); ?></p>
                    </div>
                </div>

                <div class="method-source-selector" style="margin-bottom: 25px; padding: 15px; background: #fafafa; border: 1px solid #e5e5e5; border-radius: 4px; display: flex; gap: 20px; align-items: center;">
                    <span style="font-weight: 600; color: #3c434a;"><?php esc_html_e('Selection Method:', 'accepted-payment-methods'); ?></span>
                    <label style="font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                        <input type="radio" name="dwk_apm_source" value="manual" <?php checked($source, 'manual'); ?>>
                        <?php esc_html_e('Manual Upload', 'accepted-payment-methods'); ?>
                    </label>
                    <label style="font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                        <input type="radio" name="dwk_apm_source" value="library" <?php checked($source, 'library'); ?>>
                        <?php esc_html_e('Pre-built Library', 'accepted-payment-methods'); ?>
                    </label>
                </div>

                <!-- Manual Upload Layout Container -->
                <div id="manual-upload-container" style="display: <?php echo $source === 'manual' ? 'block' : 'none'; ?>;">
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
                    <div id="upload-icon-preview" style="display: none; align-items: center; gap: 5px; margin-bottom: 10px;">
                        <img src="" alt="Preview" style="max-height: 40px; width: auto; border: 1px solid #ccc; padding: 2px; background: #fff;">
                        <span id="remove-preview-icon" class="dashicons dashicons-no-alt" style="color: #dc3545; cursor: pointer;" title="<?php echo esc_attr__('Remove', 'accepted-payment-methods'); ?>"></span>
                    </div>
                    <form id="add-payment-method-form" style="margin-bottom: 20px;">
                        <label for="new-payment-method"><?php esc_html_e('New Payment Method:', 'accepted-payment-methods'); ?></label>
                        <input type="text" id="new-payment-method" name="new-payment-method" placeholder="<?php esc_attr_e('Enter name or upload icon', 'accepted-payment-methods'); ?>" required>
                        <button type="button" id="upload-icon-button" class="button"><?php esc_html_e('Upload Icon', 'accepted-payment-methods'); ?></button>
                        <button type="submit" id="add-payment-method-btn" class="button"><?php esc_html_e('Add Payment Method', 'accepted-payment-methods'); ?></button>
                    </form>
                </div>

                <!-- Pre-built Library Layout Container -->
                <div id="prebuilt-library-container" style="display: <?php echo $source === 'library' ? 'block' : 'none'; ?>; margin-bottom: 25px;">
                    <p style="font-style: italic; margin-bottom: 15px; color: #646970;"><?php esc_html_e('Select the payment methods you want to display on your website:', 'accepted-payment-methods'); ?></p>
                    <div class="prebuilt-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px;">
                        <?php
                        $library_options = function_exists('dwk_apm_get_prebuilt_payment_methods') ? dwk_apm_get_prebuilt_payment_methods() : [];
                        $checked_library = get_option('dwk_apm_checked_library_methods', []);
                        if (!is_array($checked_library)) {
                            $checked_library = [];
                        }
                        foreach ($library_options as $key => $data) :
                            $is_checked = in_array($key, $checked_library);
                            $label = $data['label'];
                            $file = $data['file'];
                        ?>
                            <label class="prebuilt-item" style="display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #e5e5e5; padding: 15px; background: #fff; cursor: pointer; border-radius: 4px; position: relative; transition: all 0.2s ease-in-out; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                <input type="checkbox" class="prebuilt-checkbox" value="<?php echo esc_attr($key); ?>" <?php checked($is_checked); ?> style="position: absolute; top: 10px; right: 10px; margin: 0;">
                                <img src="<?php echo esc_url(DWKAPM_PLUGIN_URL . 'assets/icons/' . $file); ?>" alt="<?php echo esc_attr($label); ?>" style="max-height: 35px; margin-bottom: 10px; width: auto; filter: drop-shadow(0 1px 1px rgba(0,0,0,0.05));">
                                <span style="font-weight: 500; font-size: 13px; color: #2c3338;"><?php echo esc_html($label); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

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
