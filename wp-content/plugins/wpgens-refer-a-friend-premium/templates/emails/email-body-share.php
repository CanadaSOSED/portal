<?php
/**
 * Email Body for Email Share
 *
 * @see     http://wpgens.helpscoutdocs.com/article/34-how-to-edit-template-files-and-keep-them-after-plugin-update
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<tbody>
    <?php if($use_woo_template !== "yes") { ?>
    <tr>
        <td style="background-color:<?php echo $color;?>;font-size:1px;line-height:3px" class="topBorder" height="3">&nbsp;</td>
    </tr>
    <tr>
        <td style="font-size:1px;line-height:1px" height="50">&nbsp;</td>
    </tr>
    <?php } ?>
    <tr>
        <td align="center" valign="top" style="padding-bottom:40px" class="imgHero"><a href="#" style="text-decoration:none" target="_blank"><img alt="" border="0" src="<?php echo WPGENS_RAF_URL. 'assets/img/hand.png'; ?>" style="width:100%;max-width:300px;height:auto;display:block" width="300"></a></td>
    </tr>
    <tr>
        <td align="center" valign="top" style="padding-bottom:5px;padding-left:20px;padding-right:20px" class="title">
            <h2 class="bigTitle" style="color:#313131;font-family:&#39;Open Sans&#39;,Helvetica,Arial,sans-serif;font-size:26px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:34px;text-align:center;padding:0;margin:0"><?php echo $heading; ?></h2></td>
    </tr>
    <tr>
        <td style="font-size:1px;line-height:1px" height="25">&nbsp;</td>
    </tr>
    <tr>
        <td align="center" valign="top" style="padding-left:20px;padding-right:20px" class="subTitle">
            <p style="font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:24px;text-align:center;padding:0;margin:0;"><?php echo $user_message; ?></p></td>
    </tr>
    <tr>
        <td style="font-size:1px;line-height:1px" height="30">&nbsp;</td>
    </tr>
    <tr>
        <td align="center" valign="top" style="padding-bottom:5px;padding-left:20px;padding-right:20px" class="btnCard">
            <table border="0" cellpadding="0" cellspacing="0" align="center">
                <tbody>
                    <tr>
                        <td align="center" width="200" height="40" bgcolor="<?php echo $color;?>" style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block;">
                            <a href="<?php echo $refLink; ?>" style="font-size:16px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; text-decoration: none; text-transform:uppercase;line-height:40px; width:100%; display:inline-block"><span style="color: #FFFFFF"><?php _e('Shop Now','gens-raf'); ?></span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="font-size:1px;line-height:1px" height="50">&nbsp;</td>
    </tr>
</tbody>