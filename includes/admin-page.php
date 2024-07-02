<?php

function apm_add_admin_menu() {
    add_menu_page( 
        'Accepted Payment Methods', 
        'Accepted Payment Methods', 
        'manage_options', 
        'accepted-payment-methods', 
        'apm_admin_page', 
        'dashicons-admin-generic' 
    );
}
add_action( 'admin_menu', 'apm_add_admin_menu' );

function apm_admin_page() {
    $default_methods = array(
        'paypal' => APM_PLUGIN_URL . 'assets/icons/paypal.svg',
        'mastercard' => APM_PLUGIN_URL . 'assets/icons/mastercard.svg',
        'american-express' => APM_PLUGIN_URL . 'assets/icons/american-express.svg',
        'visa' => APM_PLUGIN_URL . 'assets/icons/visa.svg'
    );
    $methods = get_option('payment_methods', $default_methods);
    if (!is_array($methods)) {
        $methods = $default_methods;
    }

    $default_settings = array('alignment' => 'left', 'size' => 'medium', 'tooltip' => 'yes');
    $settings = get_option('apm_settings', $default_settings);
    $settings = wp_parse_args($settings, $default_settings);
    ?>
    <div class="wrap apm-admin-page">
        <h1>Accepted Payment Methods</h1>
        <h2 class="nav-tab-wrapper">
            <a href="#tab-1" class="nav-tab nav-tab-active">Accepted Payment Methods</a>
            <a href="#tab-2" class="nav-tab">Settings</a>
        </h2>
        <div id="tab-1" class="tab-content">
            <h3>Accepted Payment Methods</h3>
            <ul id="payment-methods-list">
                <?php foreach ($methods as $method => $icon_url): ?>
                    <li class="payment-method-item" data-method="<?php echo esc_attr($method); ?>">
                        <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr(ucfirst($method)); ?>">
                        <span><?php echo ucfirst(str_replace('-', ' ', $method)); ?></span>
                        <button class="remove-method">Remove</button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form id="add-payment-method-form">
                <label for="new-payment-method">New Payment Method:</label>
                <input type="text" id="new-payment-method" name="new-payment-method" required>
                <button id="upload-icon-button">Upload Icon</button>
                <input type="hidden" id="upload-icon" name="upload-icon" required>
                <button type="submit" id="add-payment-method-btn">Add Payment Method</button>
            </form>
            <button id="save-payment-methods" class="button">Save</button>
            <p id="apm-save-message" class="hidden"></p>
        </div>
        <div id="tab-2" class="tab-content" style="display:none;">
            <h3>Settings</h3>
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
                    </tr>
                    <tr valign="top">
                        <th scope="row">Size</th>
                        <td>
                            <select name="apm_settings[size]">
                                <option value="small" <?php selected($settings['size'], 'small'); ?>>Small</option>
                                <option value="medium" <?php selected($settings['size'], 'medium'); ?>>Medium</option>
                                <option value="large" <?php selected($settings['size'], 'large'); ?>>Large</option>
                            </select>
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
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
    </div>
    <?php
}
