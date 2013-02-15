<h2><?php _e('Statistics', ABCOUNTERTD); ?></h2>
<p><?php _e('These statistics show the amound of page views and unique visitors having adblock enabled.', ABCOUNTERTD); ?></p>
<table id="adblock-counter-statistic">
    <thead></thead>
    <tbody>
        <tr>
            <th><?php _e('method/value'); ?></th>
            <th><?php _e('absolute number', ABCOUNTERTD); ?></th>
            <th><?php _e('relative number', ABCOUNTERTD); ?></th>
        </tr>
        <tr>
            <th><?php _e('total page views', ABCOUNTERTD); ?></th>
            <td><?php echo get_option('abc_page_views', 0); ?></td>
            <td></td>
            <td><?php _e('total number of page views', ABCOUNTERTD); ?></td>
        </tr>
        <tr>
            <th><?php _e('total unique visitors', ABCOUNTERTD); ?></th>
            <td><?php echo get_option('abc_unique_visitors', 0); ?></td>
            <td></td>
            <td><?php _e('total number of unique visitors', ABCOUNTERTD); ?></td>
        </tr>
        <tr class="headline"><th colspan="3"><?php _e('method: include script js/advertisement.js', ABCOUNTERTD); ?></th></tr>
        <tr>
            <th><?php _e('page views', ABCOUNTERTD); ?></th>
            <td><?php echo get_option('abc_page_views_jsFile', 0); ?></td>
            <td><?php echo round( get_option('abc_page_views_jsFile', 0) / get_option('abc_page_views', 1) * 100); ?>%</td>
            <td><?php _e('total number of page views with ad blocker', ABCOUNTERTD); ?></td>
        </tr>
        <tr>
            <th><?php _e('unique visitors', ABCOUNTERTD); ?></th>
            <td><?php echo get_option('abc_unique_visitors_jsFile', 0); ?></td>
            <td><?php echo round( get_option('abc_unique_visitors_jsFile', 0) / get_option('abc_unique_visitors', 1) * 100 ); ?>%</td>
            <td><?php _e('total number of unique visitors with ad blocker', ABCOUNTERTD); ?></td>
        </tr>
    </tbody>
</table>
