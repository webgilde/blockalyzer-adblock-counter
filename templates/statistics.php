<h2><?php _e('Statistics', ABCOUNTERTD); ?></h2>
<p><?php _e('These statistics show the amound of page views and unique visitors having adblock enabled.', ABCOUNTERTD); ?></p>
<?php $status = ( in_array( 'basic', $this->_active_stat_methods )) ? __('active', ABCOUNTERTD) : __('deactivated', ABCOUNTERTD); ?>
<p><?php printf( __('Current status of this method of measurement: <strong>%s</strong>', ABCOUNTERTD ), $status ); ?></p>
<p><?php printf( __('Last reset: %s', ABCOUNTERTD), date_i18n( _x('d.m.Y, g:i a', 'time format of the last stat reset', ABCOUNTERTD), get_option('abc_last_reset', 0))); ?></p>
<table id="adblock-counter-statistic">
    <thead></thead>
    <tbody>
        <tr>
            <th></th>
            <th><?php _e('total', ABCOUNTERTD); ?></th>
            <th><?php _e('with AdBlock', ABCOUNTERTD); ?></th>
            <th><?php _e('share of AdBlock users', ABCOUNTERTD); ?></th>
        </tr>
        <tr>
            <th><?php _e('page views', ABCOUNTERTD); ?></th>
            <td><?php echo $abc_page_views = get_option('abc_page_views', 0); ?></td>
            <td><?php echo $abc_page_views_blocked = get_option('abc_page_views_blocked', 0); ?></td>
            <td><?php 
            if ( $abc_page_views > 0 ) 
                echo round( $abc_page_views_blocked / $abc_page_views * 100); 
            else echo 0; ?>%</td>
        </tr>
        <tr>
            <th><?php _e('unique visitors', ABCOUNTERTD); ?></th>
            <td><?php echo $abc_unique_visitors = get_option('abc_unique_visitors', 0); ?></td>
            <td><?php echo $abc_unique_visitors_blocked = get_option('abc_unique_visitors_blocked', 0); ?></td>
            <td><?php 
            if ( $abc_unique_visitors > 0 ) 
                echo round( $abc_unique_visitors_blocked / $abc_unique_visitors * 100); 
            else echo 0; ?>%</td>
        </tr>
        <?php /*
        <tr class="headline"><th colspan="3"><?php _e('method: include script js/advertisement.js', ABCOUNTERTD); ?></th></tr>
        <tr>
            <th><?php _e('page views', ABCOUNTERTD); ?></th>
            <td><?php echo get_option('abc_page_views_jsFile', 0); ?></td>
            <td><?php 
            if ( $abc_page_views > 0 ) 
                echo round( get_option('abc_page_views_jsFile', 0) / $abc_page_views * 100); 
            else echo 0; ?>%</td>
            <td><?php _e('total number of page views with ad blocker', ABCOUNTERTD); ?></td>
        </tr>
        <tr>
            <th><?php _e('unique visitors', ABCOUNTERTD); ?></th>
            <td><?php echo get_option('abc_unique_visitors_jsFile', 0); ?></td>
            <td><?php 
            if ( $abc_unique_visitors > 0 ) 
                echo round( get_option('abc_unique_visitors_jsFile', 0) / $abc_unique_visitors * 100 ); 
            else echo 0; ?>%</td>
            <td><?php _e('total number of unique visitors with ad blocker', ABCOUNTERTD); ?></td>
        </tr>
        <tr class="headline"><th colspan="3"><?php _e('Banner', ABCOUNTERTD); ?></th></tr>
        <tr>
            <th><?php _e('banner missing', ABCOUNTERTD); ?></th>
            <td><?php echo get_option('abc_page_views_BannerFile', 0); ?></td>
            <td><?php 
            if ( $abc_page_views > 0 ) 
                echo round( get_option('abc_page_views_BannerFile', 0) / $abc_page_views * 100); 
            else echo 0; ?>%</td>
            <td><?php _e('total number of page views with no banner', ABCOUNTERTD); ?></td>
        </tr>
        <tr>
            <th><?php _e('unique visitors', ABCOUNTERTD); ?></th>
            <td><?php echo get_option('abc_unique_visitors_BannerFile', 0); ?></td>
            <td><?php 
            if ( $abc_unique_visitors > 0 ) 
                echo round( get_option('abc_unique_visitors_BannerFile', 0) / $abc_unique_visitors * 100 ); 
            else echo 0; ?>%</td>
            <td><?php _e('total number of unique visitors with ad blocker', ABCOUNTERTD); ?></td>
        </tr> */ ?>
        
    </tbody>
</table>
<div class="abc-form-block">
    <form action="" method="post">
        <input type="hidden" name="abcounter" value="reset"/>
        <input type="submit" value="<?php _e('reset statistics', ABCOUNTERDIR ); ?>"/>
    </form>
    <p class="description"><?php _e('Resets statistics. No way to turn back.', ABCOUNTERTD); ?></p>
</div>

<h2>Statistics</h2><?php
require_once( ABCOUNTERPATH . '/classes/tracking.php' );
ABC_Tracking::send();
?>


<?php do_action('abc_stats'); ?>