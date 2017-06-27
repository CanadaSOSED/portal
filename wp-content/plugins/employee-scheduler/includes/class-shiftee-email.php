<?php

/**
 * Send HTML email.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/includes
 */

/**
 * Send HTML email.
 *
 * @since      2.0.0
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/includes
 * @author     Range <support@shiftee.co>
 */
class Shiftee_Email {

	/**
	 * Get "From" email.
	 *
	 * Check options for notification "from" field, and if it isn't set, use the site admin email.
	 *
	 * @since 1.5
	 *
	 * @return string "From" email header.
	 */
	public function email_from() {

		$options = get_option( 'wpaesm_options ');
		$from = '';
		if( isset( $options['notification_from_name'] ) ) {
			$from .= $options['notification_from_name'];
		} else {
			$from .= get_bloginfo( 'name' );
		}

		if( isset( $options['notification_from_email'] ) ) {
			$from .= " <" . sanitize_email( $options['notification_from_email'] ) . ">";
		} else {
			$from .= " <" . sanitize_email( get_bloginfo( 'admin_email' ) ) . ">";
		}

		return $from;

	}

	/**
	 * Send HTML email.
	 *
	 * Send a nicely-formatted HTML email.
	 *
	 * @since 1.5
	 *
	 * @link URL
	 *
	 * @param string  $from  Name and email address for "from" header.
	 * @param string  $to  Name and email address for "to" header.
	 * @param string  $cc  Name and email address for "cc" header.
	 * @param string  $subject  Subject of email.
	 * @param string  $message_text  HTML-formatted message text.
	 * @return bool True if email sent successfully.
	 */
	function send_email( $from, $to, $cc, $subject, $message_text ) {

		/* translators: use this to change the character encoding in emails */
		$charset = __( 'ISO-8859-1', 'employee-scheduler' );

		$headers = "From: " . $from . " \r\n";
		if( isset( $cc ) && '' !== $cc ) {
			$headers .= "Cc: " . $cc . "\r\n";
		}
		$headers .= "MIME-Version: 1.0\r\n";

		$headers .= "Content-Type: text/html; charset=" . $charset . "\r\n";

		$subject = $subject;

		$message = '';

		$message .= '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>' . __( 'Email From Shiftee', 'employee-scheduler' ) . '</title>
        <style>
        /* -------------------------------------
                GLOBAL
        ------------------------------------- */
        * {
            margin: 0;
            padding: 0;
            font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
            font-size: 100%;
            line-height: 1.6;
        }

        img {
            max-width: 100%;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100%!important;
            height: 100%;
        }


        /* -------------------------------------
                ELEMENTS
        ------------------------------------- */
        a {
            color: #0F75BD;
        }

        .btn-primary {
            text-decoration: none;
            color: #FFF;
            background-color: #348eda;
            border: solid #348eda;
            border-width: 10px 20px;
            line-height: 2;
            font-weight: bold;
            margin-right: 10px;
            text-align: center;
            cursor: pointer;
            display: inline-block;
            border-radius: 25px;
        }

        .btn-secondary {
            text-decoration: none;
            color: #FFF;
            background-color: #aaa;
            border: solid #aaa;
            border-width: 10px 20px;
            line-height: 2;
            font-weight: bold;
            margin-right: 10px;
            text-align: center;
            cursor: pointer;
            display: inline-block;
            border-radius: 25px;
        }

        .last {
            margin-bottom: 0;
        }

        .first {
            margin-top: 0;
        }

        .padding {
            padding: 10px 0;
        }


        /* -------------------------------------
                BODY
        ------------------------------------- */
        table.body-wrap {
            width: 100%;
            padding: 20px;
        }

        table.body-wrap .container {
            border: 1px solid #f0f0f0;
        }


        /* -------------------------------------
                FOOTER
        ------------------------------------- */
        table.footer-wrap {
            width: 100%;
            clear: both!important;
        }

        .footer-wrap .container p {
            font-size: 12px;
            color: #666;

        }

        table.footer-wrap a {
            color: #999;
        }


        /* -------------------------------------
                TYPOGRAPHY
        ------------------------------------- */
        h1, h2, h3 {
            font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            color: #000;
            margin: 40px 0 10px;
            line-height: 1.2;
            font-weight: 200;
        }

        h1 {
            font-size: 36px;
        }
        h2 {
            font-size: 28px;
        }
        h3 {
            font-size: 22px;
        }

        p, ul, ol {
            margin-bottom: 10px;
            font-weight: normal;
            font-size: 14px;
        }

        ul li, ol li {
            margin-left: 5px;
            list-style-position: inside;
        }

        small {
            font-size: 11px;
        }

        /* ---------------------------------------------------
                RESPONSIVENESS
                Nuke it from orbit. It is the only way to be sure.
        ------------------------------------------------------ */

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block!important;
            max-width: 600px!important;
            margin: 0 auto!important; /* makes it centered */
            clear: both!important;
        }

        /* Set the padding on the td rather than the div for Outlook compatibility */
        .body-wrap .container {
            padding: 20px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            max-width: 600px;
            margin: 0 auto;
            display: block;
        }

        /* Make sure tables in the content area are 100% wide */
        .content table {
            width: 100%;
        }

        </style>
        </head>

        <body bgcolor="#f6f6f6">

        <!-- body -->
        <table class="body-wrap" bgcolor="#f6f6f6">
            <tr>
                <td></td>
                <td class="container" bgcolor="#FFFFFF">

                    <!-- content -->
                    <div class="content">
                    <table>
                        <tr>
                            <td>
                                ' . $message_text . '
                            </td>
                        </tr>
                    </table>
                    </div>
                    <!-- /content -->

                </td>
                <td></td>
            </tr>
        </table>
        <!-- /body -->

        <!-- footer -->
        <table class="footer-wrap">
            <tr>
                <td></td>
                <td class="container">

                    <!-- content -->
                    <div class="content">
                        <table>
                            <tr>
                                <td align="center">
                                    <p>
                                        <a href="' . site_url() . '">' . get_bloginfo( 'name' ) . '</a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- /content -->

                </td>
                <td></td>
            </tr>
        </table>
        <!-- /footer -->

        </body>
        </html>';

		$success = wp_mail( $to, $subject, $message, $headers );

		return $success;

	}
}