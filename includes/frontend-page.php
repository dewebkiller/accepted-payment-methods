<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
if (!function_exists('dwk_apm_methods_shortcode')) {
  function dwk_apm_methods_shortcode()
  {
    $default_methods = [];
    $methods = get_option('dwk_apm_payment_methods', $default_methods);

    if (!is_array($methods)) {
      $methods = $default_methods;
    }

    $default_settings = array('alignment' => 'left', 'icon_size' => 50, 'tooltip' => 'yes', 'icon_spacing' => 10);
    $settings = get_option('dwk_apm_settings', $default_settings);
    $settings = wp_parse_args($settings, $default_settings);

    ob_start();
?>
    <div class="dwk-payment-methods">
      <ul class="dwk-payment-methods-list" style="display: flex; gap: <?php echo esc_attr($settings['icon_spacing']); ?>px; justify-content: <?php echo esc_attr($settings['alignment']); ?>;">
        <?php foreach ($methods as $method) : ?>
          <?php if (is_array($method) && isset($method['name'], $method['icon'])) : ?>
            <li class="dwk-payment-method-item dwk-tooltip" style="list-style: none;">
              <img src="<?php echo esc_url($method['icon']); ?>" alt="<?php echo esc_attr(ucfirst($method['name'])); ?>" class="dwk-payment-method-icon dwk-payment-method-icon-<?php echo esc_attr($settings['icon_size']); ?>" width="<?php echo esc_attr($settings['icon_size']); ?>" <?php if ($settings['tooltip'] === 'yes') : ?> title="<?php echo esc_attr(ucfirst($method['name'])); ?>" <?php endif; ?>><?php if ($settings['tooltip'] === 'yes') : ?>
                <span class="dwk-tooltiptext"><?php echo esc_attr(ucfirst($method['name'])); ?></span>
              <?php endif; ?>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </div>
<?php
    return ob_get_clean();
  }
}
add_shortcode('dwk_apm_methods', 'dwk_apm_methods_shortcode');
