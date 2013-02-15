<h2><?php _e('Settings', ABCOUNTERTD); ?></h2>
<p><?php _e('Settings for the AdBlock Counter', ABCOUNTERTD); ?></p>
<div class="abc-form-block">
    <form action="" method="post">
        <input type="hidden" name="abcounter" value="reset"/>
        <input type="submit" value="<?php _e('reset statistics', ABCOUNTERDIR ); ?>"/>
    </form>
    <p><?php _e('Resets the statistics and times. Also unique visitors will be counted again.', ABCOUNTERTD); ?></p>
</div>
<div class="abc-form-block">
    <form action="" method="post">
        <?php if ( $this->_is_measuring() ) : ?>
        <span><?php echo get_option('abc_start'); ?></span>
        <?php else : ?>
        <input type="hidden" name="abcounter" value="start"/>
        <input type="submit" value="<?php _e('start measuring', ABCOUNTERDIR ); ?>"/>
        <?php endif; ?>
    </form>
    <p><?php _e('Starts the measuring (again). Sets the start time.', ABCOUNTERTD); ?></p>
</div>
<div class="abc-form-block">
    <form action="" method="post">
        <?php $stop = get_option('abc_stop'); 
        if ( !empty( $stop ) ) : ?>
        <span><?php echo get_option('abc_stop'); ?></span>
        <?php else : ?>
        <input type="hidden" name="abcounter" value="stop"/>
        <input type="submit" value="<?php _e('stop measuring', ABCOUNTERDIR ); ?>"/>
        <?php endif; ?>
    </form>
    <p><?php _e('Stop the measuring. Sets the end time.', ABCOUNTERTD); ?></p>
</div>