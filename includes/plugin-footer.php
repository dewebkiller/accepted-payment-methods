<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
?>
<div class="dwk-footer-wrap wrap">
  <div class="row">
    <div class="creator col-md-3">
      <span><?php esc_html_e('Proudly Created by', 'accepted-payment-methods'); ?></span>
      <a href="https://www.dewebkiller.com/" target="_blank"><img src="<?php echo esc_url(DWKAPM_PLUGIN_URL); ?>assets/icons/logo.png" alt="dewebkiller" class="dwk-logo" width="150"></a>
    </div>

    <div class="col-md-6">
      <ul class="footer-nav">
        <li><a href="https://niresh.com.np/" target="_blank"><?php esc_html_e('Website', 'accepted-payment-methods'); ?></a></li>
        <li><a href="https://www.linkedin.com/in/dewebkiller/" target="_blank"><?php esc_html_e('Linkedin', 'accepted-payment-methods'); ?></a></li>
        <li><a href="https://github.com/dewebkiller" target="_blank"><?php esc_html_e('Github', 'accepted-payment-methods'); ?></a></li>
        <li><a href="https://buymeacoffee.com/dewebkiller" target="_blank"><?php esc_html_e('Support', 'accepted-payment-methods'); ?></a></li>
        <li><a href="https://www.dewebkiller.com/" target="_blank"><?php esc_html_e('Blog', 'accepted-payment-methods'); ?></a></li>
      </ul>
    </div>

    <div class="copyright col-md-3">
      <span><?php esc_html_e('All rights reserved', 'accepted-payment-methods'); ?></span>
      &copy; <?php echo esc_js(gmdate('Y', strtotime(current_time('mysql', 1)))); ?>
    </div>
  </div>
</div>