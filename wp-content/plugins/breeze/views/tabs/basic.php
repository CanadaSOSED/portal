<?php
$basic = get_option('breeze_basic_settings');
?>
<table cellspacing="15">
    <tr>
        <td>
            <label for="cache-system"><?php _e('Cache System', 'breeze'); ?></label>
        </td>
        <td>
            <input type="checkbox" id="cache-system" name="cache-system"
                   value='1' <?php checked($basic['breeze-active'], '1') ?>/>
            <label class="breeze_tool_tip">
                <?php _e('This is the basic cache that we recommend should be kept enabled in all cases. Basic cache will build the internal and static caches for the WordPress websites.', 'breeze') ?>
            </label>
        </td>
    </tr>
    <tr>
        <td>
            <label for="cache-ttl"><?php _e('Purge cache after', 'breeze'); ?></label>
        </td>
        <td>
            <input type="text" id="cache-ttl" size="5" name="cache-ttl"
                   value='<?php echo(!empty($basic['breeze-ttl']) ? (int)$basic['breeze-ttl'] : '1440'); ?>'/>
            <label class="breeze_tool_tip" style="vertical-align: baseline">
                <?php _e('Automatically purge internal cache after X minutes. By default this is set to 1440 minutes', 'breeze') ?>
            </label>
        </td>
    </tr>
    <tr>
        <td>
            <label class="breeze_tool_tip"><?php _e('Minification', 'breeze'); ?></label>
        </td>
        <td>
            <ul>
                <li>
                    <input type="checkbox" name="minification-html"
                           value="1" <?php checked($basic['breeze-minify-html'], '1') ?>/>
                    <label class="breeze_tool_tip"><?php _e('HTML', 'breeze') ?></label>
                </li>
                <li>
                    <input type="checkbox" name="minification-css"
                           value="1" <?php checked($basic['breeze-minify-css'], '1') ?>/>
                    <label class="breeze_tool_tip"><?php _e('CSS', 'breeze') ?></label>
                </li>
                <li>
                    <input type="checkbox" name="minification-js"
                           value="1" <?php checked($basic['breeze-minify-js'], '1') ?>/>
                    <label class="breeze_tool_tip"><?php _e('JS', 'breeze') ?></label>
                </li>
                <li>
                    <label><?php _e('Check the above boxes to minify HTML, CSS, or JS files.', 'breeze') ?></label>
                    <br>
                    <label><b>Note:&nbsp;</b>
                        <span style="color: #ff0000"><?php _e('We recommend testing minification on a staging website before deploying it on a live website. Minification is known to cause issues on the frontend.', 'breeze') ?></span>
                    </label>
                </li>
            </ul>

        </td>
    </tr>
    <tr>
        <td>
            <label for="gzip-compression"><?php _e('Gzip Compression', 'breeze') ?></label>
        </td>
        <td>
            <input type="checkbox" id="gzip-compression" name="gzip-compression"
                   value='1' <?php checked($basic['breeze-gzip-compression'], '1') ?>/>
            <label class="breeze_tool_tip"><?php _e('Enable this to compress your files making HTTP requests fewer and faster.', 'breeze') ?></label>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle">
            <label for="browser-cache"><?php _e('Browser Cache', 'breeze') ?></label>
        </td>
        <td>
            <input type="checkbox" id="browser-cache" name="browser-cache"
                   value='1' <?php checked($basic['breeze-browser-cache'], '1') ?>/>
            <label class="breeze_tool_tip"><?php _e('Enable this to add expires headers to static files. This will ask browsers to either request a file from server or fetch from the browser’s cache.', 'breeze') ?></label>
        </td>
    </tr>

    <tr style="display: none;">
        <td style="vertical-align: middle">
            <label for="desktop-cache" class="breeze_tool_tip"> <?php _e('Desktop Cache', 'breeze') ?></label>
        </td>
        <td>
            <select id="desktop-cache" name="desktop-cache">
                <option value="1" <?php echo ($basic['breeze-desktop-cache'] == '1') ? 'selected="selected"' : '' ?>><?php _e('Activated', 'breeze') ?></option>
                <option value="2" <?php echo ($basic['breeze-desktop-cache'] == '2') ? 'selected="selected"' : '' ?>><?php _e('No cache for desktop', 'breeze') ?></option>
            </select>
        </td>
    </tr>

    <tr style="display: none;">
        <td style="vertical-align: middle">
            <label for="mobile-cache" class="breeze_tool_tip"> <?php _e('Mobile Cache', 'breeze') ?></label>
        </td>
        <td>
            <select id="mobile-cache" name="mobile-cache">
                <option value="1" <?php echo ($basic['breeze-mobile-cache'] == '1') ? 'selected="selected"' : '' ?>><?php _e('Automatic (same as desktop)', 'breeze') ?></option>
                <option value="2" <?php echo ($basic['breeze-mobile-cache'] == '2') ? 'selected="selected"' : '' ?>><?php _e('Specific mobile cache', 'breeze') ?></option>
                <option value="3" <?php echo ($basic['breeze-mobile-cache'] == '3') ? 'selected="selected"' : '' ?>><?php _e('No cache for mobile', 'breeze') ?></option>
            </select>
        </td>
    </tr>
</table>
