<h2><?php _e('Statistics', BATD); ?></h2>
<p><?php _e('These statistics show the amound of page views and unique visitors having adblock enabled.', BATD); ?></p>
<?php $status = ( in_array( 'basic', $this->_active_stat_methods )) ? __('active', BATD) : __('deactivated', BATD); ?>
<p><?php printf( __('Current status of this method of measurement: <strong>%s</strong>', BATD ), $status ); ?></p>
<p><?php printf( __('Last reset: %s', BATD), date_i18n( _x('d.m.Y, g:i a', 'time format of the last stat reset', BATD), get_option('ba_last_reset', 0))); ?></p>
<?php if ( get_option('ba_last_sent', 0) ) : 
    ?><p><?php printf( __('Last time compared: %s', BATD), 
            date_i18n( _x('d.m.Y, g:i a', 'time format of the last stat reset', BATD), 
                    get_option('ba_last_sent', 0))); 
    ?></p><?php 
    endif;
?><table id="adblock-counter-statistic">
    <thead></thead>
    <tbody>
        <tr>
            <th></th>
            <th><?php _e('total', BATD); ?></th>
            <th><?php _e('with AdBlock', BATD); ?></th>
            <th><?php _e('share of AdBlock users', BATD); ?></th>
            <?php if ( !empty( $this->_compare_data->general->totalViews ) ) : ?><th><?php _e('general benchmark', BATD); ?></th><?php endif; ?>
            <?php if ( !empty( $this->_compare_data->category->totalViews ) ) : ?><th><?php _e('category benchmark', BATD); ?></th><?php endif; ?>
        </tr>
        <tr>
            <th><?php _e('page views', BATD); ?></th>
            <td><?php echo $ba_page_views = get_option('ba_page_views', 0); ?></td>
            <td><?php echo $ba_page_views_blocked = get_option('ba_page_views_blocked', 0); ?></td>
            <td><?php 
            if ( $ba_page_views > 0 ) 
                echo round( $ba_page_views_blocked / $ba_page_views * 100); 
            else echo 0; ?>%</td>
            <?php if ( !empty( $this->_compare_data->general->totalViews ) ) : ?><td><?php echo round( $this->_compare_data->general->totalViews ) . '%'; ?></td><?php endif; ?>
            <?php if ( !empty( $this->_compare_data->category->totalViews ) ) : ?><td><?php echo round( $this->_compare_data->category->totalViews ) . '%'; ?></td><?php endif; ?>
        </tr>
        <tr>
            <th><?php _e('unique visitors', BATD); ?></th>
            <td><?php echo $ba_unique_visitors = get_option('ba_unique_visitors', 0); ?></td>
            <td><?php echo $ba_unique_visitors_blocked = get_option('ba_unique_visitors_blocked', 0); ?></td>
            <td><?php 
            if ( $ba_unique_visitors > 0 ) 
                echo round( $ba_unique_visitors_blocked / $ba_unique_visitors * 100); 
            else echo 0; ?>%</td>
            <?php if ( !empty( $this->_compare_data->general->totalUsers ) ) : ?><td><?php echo round( $this->_compare_data->general->totalUsers ) . '%'; ?></td><?php endif; ?>
            <?php if ( !empty( $this->_compare_data->category->totalUsers ) ) : ?><td><?php echo round( $this->_compare_data->category->totalUsers ) . '%'; ?></td><?php endif; ?>
        </tr>
    </tbody>
</table>
<div class="ba-form-block">
    <form action="" method="post">
        <input type="hidden" name="bacounter" value="reset"/>
        <input type="submit" value="<?php _e('reset statistics', BADIR ); ?>"/>
    </form>
    <p class="description"><?php _e('Resets statistics. No way to turn back.', BATD); ?></p>
</div>
<div class="ba-form-block">
    <?php if ( $this->_compare_allowed ) :
        ?><p class="success"><?php _e( 'Compare your data with others now.', BATD); ?></p><?php 
        else :
        ?><p class="warning"><?php _e( 'You can currently not compare your data with others. See HELP panel above for more information.', BATD); ?></p><?php endif; ?>    
        <p><?php printf( __('By clicking the button below, you accept its <a href="%s">terms and privacy policy</a>.', BATD ), 'http://webgilde.com/en/blockalyzer-privacy-policy/'); ?></p>
        <form action="" method="post">
        <input type="hidden" name="bacounter" value="compare"/>
        <input type="submit" value="<?php _e('compare statistics', BADIR ); ?>"<?php if ( !$this->_compare_allowed ) echo ' disabled="disabled"'; ?>/>
    </form>
    <p class="description"><?php _e('Compare your statistics with the statistics of other pages. Will also send your data to our server.', BATD); ?><br/>
    <?php _e('See the HELP panel above for information on which data we are sending.', BATD); ?></p>
    <p><strong><?php _e('Site Topic', BATD); ?></strong>: <?php
        $category = $this->_options['benchmark_category'];
        if ( empty( $category ) ) printf(__('You did not specify a site topic. You will receive only the general stats. Visit the <a href="%s">settings page</a> to specify the topic of your site.', BATD), admin_url('options-general.php?page=ba-settings-page'));
        else {
            $site_categories = $this->get_site_categories();
            // include( BAPATH . 'inc/site_categories.php' );
            if ( !empty( $site_categories[ $category ]) ) {
                echo $site_categories[ $category ];
            }
        }
    ?></p>
    <p><strong><?php _e('Localization', BATD ); ?></strong>: <?php echo get_locale(); ?></p>
</div>

<?php do_action('ba_stats'); ?>