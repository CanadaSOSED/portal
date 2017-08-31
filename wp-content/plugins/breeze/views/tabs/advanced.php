<?php
    $advanced = get_option('breeze_advanced_settings');
?>
<table cellspacing="15">
    <tr>
        <td>
            <label for="exclude-urls" class="breeze_tool_tip"><?php _e('Never Cache these URLs', 'breeze'); ?></label>
        </td>
        <td>
            <textarea cols="100" rows="7" id="exclude-urls"
                         name="exclude-urls"><?php if (!empty($advanced['breeze-exclude-urls'])) {
                    $output = implode("\n", $advanced['breeze-exclude-urls']);
                    echo esc_textarea($output);
                } ?></textarea>
            <br>
            <label class="breeze_tool_tip"><b>Note:&nbsp;</b><?php _e('Add the URLs of the pages (one per line) you wish to exclude from the WordPress internal cache. To exclude URLs from the Varnish cache, please refer to this ', 'breeze') ?><a href="https://support.cloudways.com/how-to-exclude-url-from-varnish/" target="_blank"><?php _e('Knowledge Base','breeze')?></a><?php _e(' article.','breeze')?> </label>
        </td>
    </tr>
    <tr>
        <td>
            <label class="breeze_tool_tip"><?php _e('Group Files', 'breeze'); ?></label>
        </td>
        <td>
            <ul>
                <li>
                    <input type="checkbox" name="group-css" value="1" <?php checked($advanced['breeze-group-css'],'1')?>/>
                    <label class="breeze_tool_tip"><?php _e('CSS','breeze')?></label>
                </li>
                <li>
                    <input type="checkbox" name="group-js" value="1" <?php checked($advanced['breeze-group-js'],'1')?>/>
                    <label class="breeze_tool_tip"><?php _e('JS','breeze')?></label>
                </li>
                <li>
                    <label class="breeze_tool_tip">
                        <b>Note:&nbsp;</b><?php _e('Group CSS and JS files to combine them into a single file. This will reduce the number of HTTP requests to your server.', 'breeze') ?><br>
                        <b><?php _e('Important: Enable Minification to use this option.','breeze')?></b>
                    </label>
                </li>
            </ul>
        </td>
    </tr>
    <tr>
        <td>
            <label for="exclude-css" class="breeze_tool_tip"><?php _e('Exclude CSS', 'breeze') ?></label>
        </td>
        <td>
            <textarea cols="100" rows="7" id="exclude-css"
                      name="exclude-css"><?php if (!empty($advanced['breeze-exclude-css'])) {
                    $output = implode("\n", $advanced['breeze-exclude-css']);
                    echo esc_textarea($output);
                } ?></textarea>
            <br>
            <label class="breeze_tool_tip"><b>Note:&nbsp;</b><?php _e('Use this option to exclude CSS files from Minification and Grouping. Enter the URLs of CSS files on each line.', 'breeze') ?></label>
        </td>
    </tr>
    <tr>
        <td>
            <label for="exclude-js" class="breeze_tool_tip"><?php _e('Exclude JS', 'breeze') ?></label>
        </td>
        <td>
            <textarea cols="100" rows="7" id="exclude-js"
                      name="exclude-js"><?php if (!empty($advanced['breeze-exclude-js'])) {
                    $output = implode("\n", $advanced['breeze-exclude-js']);
                    echo esc_textarea($output);
                } ?></textarea>
            <br>
            <label class="breeze_tool_tip"><b>Note:&nbsp;</b><?php _e('Use this option to exclude JS files from Minification and Grouping. Enter the URLs of JS files on each line.', 'breeze') ?></label>
        </td>
    </tr>
</table>
