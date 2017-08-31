<?php
    $varnish = get_option('breeze_varnish_cache');
    $check_varnish = Breeze_Admin::check_varnish();
?>
<div class="breeze-top-notice">
   <label class="breeze_tool_tip"><?php _e('By default Varnish is enabled on all WordPress websites hosted on Cloudways.','breeze')?></label>
</div>
<table cellspacing="15">
    <tr>
        <td>
            <label for="auto-purge-varnish" class="breeze_tool_tip"><?php _e('Auto Purge Varnish', 'breeze'); ?></label>
        </td>
        <td>
            <input type="checkbox" id="auto-purge-varnish" name="auto-purge-varnish" value="1"  <?php checked($varnish['auto-purge-varnish'], '1')?>/>
            <label class="breeze_tool_tip" ><?php _e('Keep this option enabled to automatically purge Varnish cache on actions like publishing new blog posts, pages and comments.','breeze')?></label>
            <br>
            <?php if( !$check_varnish): ?>
            <label><b>Note:&nbsp;</b>
                <span style="color: #ff0000"><?php _e('Seems Varnish is disabled on your Application. Please refer to ', 'breeze') ?><a href="https://support.cloudways.com/most-common-varnish-issues-and-queries/" target="_blank">this KB</a><?php _e(' and learn how to enable it.','breeze') ?> </span>
            </label>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>
            <label for="varnish-server-ip" class="breeze_tool_tip"><?php _e('Varnish server', 'breeze'); ?></label>
        </td>
        <td>
            <input type="text" id="varnish-server-ip" size="20" name="varnish-server-ip"
                   value='<?php echo(!empty($varnish['breeze-varnish-server-ip']) ? esc_html($varnish['breeze-varnish-server-ip']) : '127.0.0.1'); ?>'/>
            <br/><span class="breeze_tool_tip" ><strong><?php _e('Note: Keep this default if you are a Cloudways customer. Otherwise ask your hosting provider on what to set here.','breeze')?></strong></span>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle">
            <label class="breeze_tool_tip"><?php _e('Purge Varnish Cache', 'breeze'); ?></label>
        </td>
        <td>
            <input type="button" id="purge-varnish-button" class="button" value="<?php _e('Purge','breeze')?>"  />
            <label style="vertical-align: bottom"><?php _e('Use this option to instantly Purge Varnish Cache on entire website. ', 'breeze') ?></label>
        </td>
    </tr>
</table>