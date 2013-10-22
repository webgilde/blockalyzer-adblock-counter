<table id="ba_dashboard_table">
    <tbody>
        <?php $blocked = round( get_option('ba_unique_visitors_blocked', 0) / get_option('ba_unique_visitors', 0) * 100, 1); ?>
        <?php $benchmark_general = (isset($this->_compare_data->general->totalUsers)) ? round( $this->_compare_data->general->totalUsers, 1 ) : 0; ?>
        <?php $benchmark_category = (isset($this->_compare_data->category->totalUsers)) ? round( $this->_compare_data->category->totalUsers, 1 ) : 0; ?>
        <tr><th><?php _e('visitors with AdBlocker', BATD); ?></th><td class="<?php if($benchmark_general && ($blocked > $benchmark_general || ($blocked > $benchmark_category))) echo 'warning'; else echo 'success'; ?>"><?php echo $blocked; ?>%</td></tr>
        <tr><th><?php _e('general benchmark', BATD); ?></th><td><?php echo $benchmark_general; ?>%</td></tr>
        <tr><th><?php _e('category benchmark', BATD); ?></th><td><?php echo $benchmark_category; ?>%</td></tr>
        <?php if(!in_array( 'basic', $this->_active_stat_methods )) : ?><tr><th><?php _e('active', BATD); ?></th><td><?php _e('deactivated', BATD); ?></td></tr><?php endif; ?>
    </tbody>
</table>
<?php if ( $this->_compare_allowed ) : ?><p class="success"><?php _e('You can now compare your data with others', BATD); ?></p><?php endif; ?>
<p>
    <a href="<?php echo admin_url('tools.php?page=adblock-counter'); ?>"><?php _e('show details', BATD); ?><a> | 
    <a href="<?php echo admin_url('tools.php?page=adblock-counter#compare-anchor'); ?>"><?php _e('compare data', BATD); ?><a> | 
    <a href="<?php echo admin_url('options-general.php?page=ba-settings-page'); ?>"><?php _e('configure BlockAlyzer', BATD); ?><a>
</p>