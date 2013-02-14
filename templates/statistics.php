<p><?php _e('This is a statistics that shows the amound of page views and unique visitors having adblock enabled.'); ?></p>
<table>
    <thead></thead>
    <tbody>
        <tr><th><?php _e('method/value'); ?></th><th><?php _e('absolute number'); ?></th><th><?php _e('relative number'); ?></th></tr>
        <tr><th><?php _e('total number of page views'); ?></th><td><?php echo get_option('abc_page_views', 0); ?></td></tr>
        <tr><th><?php _e('total number of unique visitors'); ?></th><td><?php echo get_option('abc_unique_visitors', 0); ?></td></tr>
        <tr><th colspan="3"><?php _e('method: include script js/advertisement.js'); ?></th></tr>
        <tr>
            <th><?php _e('page views with ad blocker'); ?></th>
            <td><?php echo get_option('abc_page_views_jsFile', 0); ?></td>
            <td><?php echo get_option('abc_page_views_jsFile', 0) / get_option('abc_page_views', 1) * 100; ?>%</td>
        </tr>
        <tr>
            <th><?php _e('unique visitors with ad blocker'); ?></th>
            <td><?php echo round( get_option('abc_unique_visitors_jsFile', 0) ); ?></td>
            <td><?php echo round( get_option('abc_unique_visitors_jsFile', 0) / get_option('abc_unique_visitors', 1) * 100 ); ?>%</td>
        </tr>
    </tbody>
</table>
