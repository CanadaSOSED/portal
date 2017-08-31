<?php
    $cdn_integration = get_option('breeze_cdn_integration');

    $cdn_content_value = '';
    $cdn_exclude_content_value = '';
    if(!empty($cdn_integration['cdn-content'])){
        $cdn_content_value = implode(',',$cdn_integration['cdn-content']);
    }
    if(!empty($cdn_integration['cdn-exclude-content'])){
        $cdn_exclude_content_value = implode(',',$cdn_integration['cdn-exclude-content']);
    }
?>
<table cellspacing="15">
    <tr>
        <td>
            <label for="activate-cdn" class="breeze_tool_tip"><?php _e('Activate CDN', 'breeze')?></label>
        </td>
        <td>
            <input type="checkbox" id="activate-cdn" name="activate-cdn"  value="1" <?php checked($cdn_integration['cdn-active'],'1')?>/>
            <label class="breeze_tool_tip"><?php _e('Enable to make CDN effective on your website.', 'breeze')?></label>
        </td>
    </tr>
    <tr>
        <td>
            <label for="cdn-url" class="breeze_tool_tip"><?php _e('CDN CNAME', 'breeze')?></label>
        </td>
        <td>
            <input type="text" id="cdn-url" name="cdn-url" size="50" placeholder="<?php _e('https://www.domain.com','breeze')?>" value="<?php echo (($cdn_integration['cdn-url'])?esc_html($cdn_integration['cdn-url']):''); ?>"/>
            <label style="vertical-align: baseline" class="breeze_tool_tip"><?php _e('Enter the URL of CDN.', 'breeze')?></label>
            <br>
            <label class="breeze_tool_tip"><b>Note:&nbsp;</b><?php _e('Use double slash ‘//’ at the start or url, if you have some pages on  HTTP and some are on HTTPS.', 'breeze') ?></label>
        </td>
    </tr>
    <tr>
        <td>
            <label for="cdn-content" class="breeze_tool_tip" ><?php _e('CDN Content', 'breeze')?></label>
        </td>
        <td>
            <input type="text" id="cdn-content" name="cdn-content" size="50" value="<?php echo (($cdn_content_value)?esc_html($cdn_content_value):''); ?>"/>
            <br>
            <label class="breeze_tool_tip"><b>Note:&nbsp;</b><?php _e('Enter the directories (comma separated) of which you want the CDN to serve the content.', 'breeze') ?></label>
        </td>
    </tr>
    <tr>
        <td>
            <label for="cdn-exclude-content" class="breeze_tool_tip" ><?php _e('Exclude Content', 'breeze')?></label>
        </td>
        <td>
            <input type="text" id="cdn-exclude-content" name="cdn-exclude-content" size="50" value="<?php echo (($cdn_exclude_content_value)?esc_html($cdn_exclude_content_value):''); ?>" />
            <br>
            <label class="breeze_tool_tip"><b>Note:&nbsp;</b><?php _e('Exclude file types or directories from CDN. Example, enter .css to exclude the CSS files.', 'breeze') ?></label>
        </td>
    </tr>
    <tr>
        <td>
            <label for="cdn-relative-path" class="breeze_tool_tip" ><?php _e('Relative path', 'breeze')?></label>
        </td>
        <td>
            <input type="checkbox" id="cdn-relative-path" name="cdn-relative-path"  value="1" <?php checked($cdn_integration['cdn-relative-path'], '1')?>/>
            <label class="breeze_tool_tip"><?php _e('Keep this option enabled. Use this option to enable relative path for your CDN on your WordPress site.', 'breeze')?></label>
        </td>
    </tr>
</table>