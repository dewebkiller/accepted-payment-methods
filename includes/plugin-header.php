<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
?>
<div class="dwk-header-wrap wrap">
  <div class="row">
    <div class="logo-wrap col-md-6">
      <div class="title">
        <a href="https://www.dewebkiller.com/" target="_blank"><img src="<?php echo esc_url(DWKAPM_PLUGIN_URL); ?>assets/icons/logo.png" alt="dewebkiller" class="dwk-logo" width="200"></a>
        
      </div>
    </div>

    <div class="btn-wrap col-md-6">
      <a href="https://buymeacoffee.com/dewebkiller/" target="_blank" class="dwk-btn dashicons-before dashicons-heart"> <?php esc_html_e('Support', 'accepted-payment-methods'); ?></a>
      <a href="#" class="dwk-btn btn2 dashicons-before dashicons-star-filled"><?php esc_html_e('Rate us', 'accepted-payment-methods'); ?></a>
    </div>
  </div>
</div>
<div class="clear"></div>
<div class="dwk-notice">
  <div class="dwk-notice__aside">
  <span class="dashicons dashicons-info-outline"></span>
  </div>
  <div class="dwk-notice__content">
  <h1><?php esc_html_e('Showcase Payment Methods', 'accepted-payment-methods'); ?></h1>
    <p><?php esc_html_e('Display the payment method icons/images in you WordPress website like never before.', 'accepted-payment-methods'); ?></p>
  </div>
</div>