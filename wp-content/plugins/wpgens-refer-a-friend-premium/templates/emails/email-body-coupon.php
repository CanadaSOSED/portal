<?php
/**
 * Email Body
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
        <td align="center" valign="top" style="padding-bottom:40px" class="imgHero"><a href="#" style="text-decoration:none" target="_blank"><img alt="" border="0" src="<?php echo WPGENS_RAF_URL. 'assets/img/coupon.png'; ?>" style="width:100%;max-width:300px;height:auto;display:block" width="300"></a></td>
    </tr>
    <tr>
        <td align="center" valign="top" style="padding-bottom:5px;padding-left:20px;padding-right:20px" class="title">
            <h2 class="bigTitle" style="color:#313131;font-family:&#39;Open Sans&#39;,Helvetica,Arial,sans-serif;font-size:26px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:34px;text-align:center;padding:0;margin:0"><?php echo $heading; ?></h2></td>
    </tr>
    <tr>
        <td style="font-size:1px;line-height:1px" height="25">&nbsp;</td>
    </tr>
    <tr>
        <td align="center" valign="top" style="padding-bottom:35px;padding-left:20px;padding-right:20px" class="subTitle">
            <p style="font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:24px;text-align:center;padding:0;margin:0;"><?php echo $user_message; ?></p></td>
    </tr>
    <tr>
        <td align="center" valign="top" style="padding-bottom:5px;padding-left:20px;padding-right:20px" class="offerTitle">
            <p class="bigTitle" style="color:<?php echo $color; ?>;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:24px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:36px;text-align:center;padding:0;font-weight: bold;margin:0;max-width: 300px;padding: 10px;border: 2px dashed #ddd;"><?php echo $coupon_code; ?></p>
        </td>
    </tr>
    <tr>
        <td style="font-size:1px;line-height:1px" height="30">&nbsp;</td>
    </tr>
    <tr>
        <td align="center" valign="top" style="padding-bottom:5px;padding-left:20px;padding-right:20px" class="btnCard">
            <table border="0" cellpadding="0" cellspacing="0" align="center">
                <tbody>
                    <tr>
                        <td align="center" style="background-color:<?php echo $color;?>;padding-top:10px;padding-bottom:10px;padding-left: 50px;padding-right: 50px;border-radius:2px;" class="postButton"><a href="<?php echo get_home_url(); ?>" style="color:#fff;font-family:&#39;Open Sans&#39;,Helvetica,Arial,sans-serif;font-size: 16px;font-weight:600;letter-spacing:1px;text-transform:uppercase;text-decoration:none;" target="_blank"><?php _e('Shop Now','gens-raf'); ?></a></td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <?php if($expiry) { ?>
    <tr>
        <td align="center" valign="top" style="padding-bottom:40px;padding-left:20px;padding-right:20px" class="infoDate">
            <p class="midText" style="color:#333;font-family:&#39;Open Sans&#39;,Helvetica,Arial,sans-serif;font-size:11px;font-weight:700;line-height:20px;text-align:center;padding:0;margin:0"><?php _e('Expires: ','gens-raf'); echo $expiry; ?></p>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td style="font-size:1px;line-height:1px" height="10">&nbsp;</td>
    </tr>
</tbody>