<h2><?php _e('Settings', ABCOUNTERTD); ?></h2>
<p><?php _e('Settings for the AdBlock Counter', ABCOUNTERTD); ?></p>
<div class="abc-form-block">
    <form action="" method="post">
        <input type="hidden" name="abcounter[reset]" value="reset"/>
        <input type="submit" value="<?php _e('reset statistics', ABCOUNTERDIR ); ?>"/>
    </form>
    <p><?php _e('Resets the statistics. Also unique visitors will be counted again.', ABCOUNTERTD); ?></p>
</div>
<div class="abc-form-block">
    <form action="" method="post">
        <input type="hidden" name="abcounter[start]" value="reset"/>
        <input type="submit" value="<?php _e('start measuring', ABCOUNTERDIR ); ?>"/>
    </form>
    <p><?php _e('Starts the measuring (again). Sets the start time.', ABCOUNTERTD); ?></p>
</div>
<div class="abc-form-block">
    <form action="" method="post">
        <input type="hidden" name="abcounter[stop]" value="reset"/>
        <input type="submit" value="<?php _e('start measuring', ABCOUNTERDIR ); ?>"/>
    </form>
    <p><?php _e('Stops the measuring. Sets the end time.', ABCOUNTERTD); ?></p>
</div>