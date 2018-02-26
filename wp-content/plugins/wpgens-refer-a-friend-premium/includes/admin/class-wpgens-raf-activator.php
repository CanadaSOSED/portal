<?php

/**
 * Fired during plugin activation.
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gens_RAF_Activator {

	/**
	 * Adding myreferrals as a new Tab/SubPage for my account page. Flushing rules.
	 *
	 * @since 2.0.0
	 */
	public static function activate() {
		add_rewrite_endpoint( 'myreferrals', EP_PAGES );
		flush_rewrite_rules();
		self::set_default_options();
	}

	/**
	 * Setting default options during plugin activation.
	 *
	 * @since 2.0.0
	 */
	public static function set_default_options() {

		if(!get_option('gens_raf_myaccount_text')) {
	        $op = "<h2>Referral Program</h2><p>For each friend you invite, we will send you a coupon code worth $20 that you can use to purchase or get a discount on any product on our site. Get started now, by sharing your referral link with your friends.</p>";
	        add_option('gens_raf_myaccount_text', $op);
	        add_option('gens_raf_share_text', $op);
	    }
	    
	    if(!get_option('gens_raf_email_body')) {
	        $op = "I thought you might like this site. I gave their products a try and I like them. Click on the link below and you will get discount on your purchase.";
	        add_option('gens_raf_email_body', $op);
	        add_option('gens_raf_email_subject_share','Check out this site!');
	    }

	    if(!get_option('gens_raf_email_message')) {
	        $op = "You invited a friend or family member to check out our shop. We are pleased to tell you they made a purchase which means you now get discount for your next order with us. <br> Use the coupon below.";
	        add_option('gens_raf_email_message', $op);
	    }

	    if(!get_option('gens_raf_buyer_email_message')) {
	        $op = "Thank you! You just made a purchase at our shop after clicking your friends referral link. And as a way of saying thank you for trusting us, we would like to give you % off of your next order with us. Use the coupon below.";
	        add_option('gens_raf_buyer_email_message', $op);
	    }

	}

}
