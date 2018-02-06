<?php
/**
 * Email Header
 *
 * @see     http://wpgens.helpscoutdocs.com/article/34-how-to-edit-template-files-and-keep-them-after-plugin-update
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
	    <meta name="viewport" content="width=device-width" />
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
	</head>
    <body style="background-color:#f9f9f9" data-gr-c-s-loaded="true">
        <center>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed;background-color:#f9f9f9" id="bodyTable">
                <tbody>
                    <tr>
                        <td align="center" valign="top" style="padding-right:10px;padding-left:10px" id="bodyCell">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="wrapperTable" style="max-width:600px">
                                <tbody>
                                    <tr>
                                        <td align="center" valign="top">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="logoTable">
                                                <tbody>
                                                    <tr>
                                                        <td align="center" valign="middle" style="padding-top:25px;padding-bottom:25px">
                                                            <?php
                                                                if ( $img = get_option( 'woocommerce_email_header_image' ) ) {
                                                                    echo '<p style="margin-top:0;margin-bottom:0;"><img width="250" src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name', 'display' ) . '" /></p>';
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="wrapperTable" style="max-width:600px">
                                <tbody>
                                    <tr>
                                        <td align="center" valign="top">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="oneColumn" style="background-color:#fff;border:1px solid #eee">