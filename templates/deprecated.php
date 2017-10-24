<?php
$plugin_data = get_plugin_data(__FILE__);
$plugins = get_plugins();
if( class_exists( 'Advanced_Ads', false ) ){
    $link = '';
} elseif( isset( $plugins['advanced-ads/advanced-ads.php'] ) ){ // is installed, but not active
    $link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) . '">'. __('Activate Now', 'advanced-ads-adsense-in-feed') .'</a>';
} else {
    $link = '<a class="button button-primary" href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . 'advanced-ads'), 'install-plugin_' . 'advanced-ads') . '">'. __('Test Advanced Ads', 'advanced-ads-adsense-in-feed') .'</a>';
}
?><div id="adblock-counter-deprecated">
    <p><?php printf(__('BlockAlyzer is deprecated. An improved ad block counter is now included in our free <a href="%s" target="_blank">Advanced Ads</a> plugin.', BATD), 'https://wordpress.org/plugins/advanced-ads/' ); ?></p>
    <p><?php echo $link; ?></p>
</div>
