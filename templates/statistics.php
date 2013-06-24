<dl class="ba-settings-list">
<?php if ( in_array( 'basic', $this->_active_stat_methods )) {
    $status = __('active', BATD);
    $statusClass = 'success';
} else {
    $status = __('deactivated', BATD);
    $statusClass = 'warning';
} ?>
<dt><?php _e('Status of this method', BATD ); ?></dt><dd class="<?php echo $statusClass; ?>"><?php echo $status; ?></dd>
<dt><?php _e('Last reset', BATD); ?></dt><dd><?php echo date_i18n( _x('d.m.Y, g:i a', 'time format of the last stat reset', BATD), get_option('ba_last_reset', 0)); ?></dd>
<?php if ( get_option('ba_last_sent', 0) ) : 
    ?><dt><?php _e('Last time compared', BATD); ?></dt><dd><?php 
    echo date_i18n( _x('d.m.Y, g:i a', 'time format of the last stat reset', BATD), get_option('ba_last_sent', 0)); ?></dd><?php    
    endif;
?></dl>
<hr class="clear"/><h2><?php _e('Statistics', BATD); ?></h2>
<p><?php _e('These statistics show the amound of page views and unique visitors having adblock enabled.', BATD); ?></p>
    <table id="adblock-counter-statistic">
    <thead>
        <tr><th></th><th><?php _e('page views', BATD); ?></th><th><?php _e('unique visitors', BATD); ?></th><th></th></tr>
    </thead>
    <tbody>
        <tr>
            <td><?php _e('total', BATD); ?></td>
            <td><?php echo $ba_page_views = get_option('ba_page_views', 0); ?></td>
            <td><?php echo $ba_unique_visitors = get_option('ba_unique_visitors', 0); ?></td>
        </tr>
        <tr>
            <td><?php _e('with ad block', BATD); ?></td>
            <td><?php echo $ba_page_views_blocked = get_option('ba_page_views_blocked', 0); ?></td>
            <td><?php echo $ba_unique_visitors_blocked = get_option('ba_unique_visitors_blocked', 0); ?></td>
        </tr>
        <tr>
            <td><?php _e('share of ad block users', BATD); ?></td>
            <td><?php 
            $ba_page_views_relative = ( $ba_page_views > 0 ) ?
                round( $ba_page_views_blocked / $ba_page_views * 100) :
                0;
            echo $ba_page_views_relative; ?>%</td>
            <td><?php 
            $ba_unique_visitors_relative = ( $ba_unique_visitors > 0 ) ?
                round( $ba_unique_visitors_blocked / $ba_unique_visitors * 100) :
                0;
            echo $ba_unique_visitors_relative; ?>%</td>
            <?php if ( $this->_compare_allowed ) : ?>
            <td><button class="compare_submit"><?php _e('load benchmark data', BATD); ?></button></td>
            <?php endif; ?>
        </tr>
        <?php if ( !empty( $this->_compare_data->general->totalViews ) ) : ?>
        <tr>
            <td><?php _e('general benchmark', BATD); ?></td>
            <?php $class = ( $this->_compare_data->general->totalViews > $ba_page_views_relative ) ? 'success' : 'warning'; ?>
            <td class="<?php echo $class; ?>"><?php echo round( $this->_compare_data->general->totalViews ) . '%'; ?></td>
            <?php $class = ( $this->_compare_data->general->totalUsers > $ba_unique_visitors_relative ) ? 'success' : 'warning'; ?>
            <td class="<?php echo $class; ?>"><?php echo round( $this->_compare_data->general->totalUsers ) . '%'; ?></td>
            <?php if ( $this->_compare_allowed ) : ?>
            <td><button class="compare_submit"><?php _e('update benchmark data', BATD); ?></button></td>
            <?php endif; ?>
        </tr>
        <?php endif; ?>
        <?php if ( !empty( $this->_compare_data->category->totalViews ) ) : ?>
        <tr>
            <td><?php _e('category benchmark', BATD); ?></td>
            <?php $class = ( $this->_compare_data->category->totalViews > $ba_page_views_relative ) ? 'success' : 'warning'; ?>
            <td class="<?php echo $class; ?>"><?php echo round( $this->_compare_data->category->totalViews ) . '%'; ?></td>
            <?php $class = ( $this->_compare_data->category->totalUsers > $ba_unique_visitors_relative ) ? 'success' : 'warning'; ?>
            <td class="<?php echo $class; ?>"><?php echo round( $this->_compare_data->category->totalUsers ) . '%'; ?></td>
            <?php if ( $this->_compare_allowed ) : ?>
            <td><button class="compare_submit"><?php _e('update benchmark data', BATD); ?></button></td>
            <?php endif; ?>
        </tr>
        <?php endif; ?>
    </tbody>
    </table>
<hr class="clear"/>
<div class="ba-form-block reset">
    <form action="" method="post">
        <input type="hidden" name="bacounter" value="reset"/>
        <input type="submit" value="<?php _e('reset statistics', BADIR ); ?>"/>
    </form>
    <p class="description"><?php _e('Resets statistics. No way to turn back.', BATD); ?></p>
</div>
<hr class="clear"/>
<h2><?php _e('Compare data', BATD); ?></h2>
<p class="description"><?php _e('Compare your statistics with the statistics of other pages. Will also send your data to our server.', BATD); ?><br/>
<?php _e('See the HELP panel above for information on which data we are sending.', BATD); ?></p>    
<div class="ba-form-block compare">
    <?php if ( $this->_compare_allowed ) :
        ?><p class="success"><?php _e( 'Compare your data with others now.', BATD); ?></p><?php 
        else :
        ?><p class="warning"><?php _e( 'You can currently not compare your data with others. See HELP panel above for more information.', BATD); ?></p><?php endif; ?>    
        <p><?php printf( __('By clicking the button below, you accept its <a href="%s">terms and privacy policy</a>.', BATD ), 'http://webgilde.com/en/blockalyzer-privacy-policy/'); ?></p>
        <form action="" method="post" id="compare_form">
            <input type="hidden" name="bacounter" value="compare"/>
            <input type="submit" value="<?php _e('compare statistics', BADIR ); ?>"<?php if ( !$this->_compare_allowed ) echo ' disabled="disabled"'; ?>/>
        </form>
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

<script>
jQuery(document).ready(function($){
    compare_form = $('#compare_form');
    $('.compare_submit').click(function(){ 
        if( confirm('<?php _e('Please confirm that you have read the terms and conditions below to send and receive data.'); ?>') ) {
            compare_form.submit();
        }
    });
});
</script>