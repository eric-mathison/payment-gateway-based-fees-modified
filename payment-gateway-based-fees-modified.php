<?php
/*
Plugin Name:  Payment Gateway Based Fees Modified
Plugin URI:   https://mathisonmedia.com
Description:  Modifies the Payment Gateway Based Fees and Discounts plugin to support WooCommerce Subscription recurring payments.
Version:      1.0.0
Author:       Eric Mathison
Author URI:   https://mathisonmedia.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) {
	return;
}

if ( ! class_exists( 'ModifyGatewayFees' ) ) :

	class ModifyGatewayFees {
		
		private $pluginFile;
		private $feesFile;
		private $search;
		private $replace;
		
		public function __construct() {
			$this->pluginFile = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/checkout-fees-for-woocommerce/checkout-fees-for-woocommerce.php';
			$this->feesFile = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/checkout-fees-for-woocommerce/includes/class-wc-checkout-fees.php';
			$this->search = 'if ( ! empty( $merged_fee ) ) {
				WC()->cart->add_fee(';
			$this->replace = 'if ( ! empty( $merged_fee ) ) {
				$the_cart->add_fee(';

			register_activation_hook( $this->pluginFile, array( $this, 'rewrite' ) );
			add_action( 'upgrader_process_complete', array( $this, 'rewrite' ) );
		}
		
		public function rewrite($strwrite) {
			$str = file_get_contents( $this->feesFile );
			$newstr = str_replace($this->search, $this->replace, $str);
			$strwrite = file_put_contents( $this->feesFile, $newstr);
		}
	}
	
endif;

new ModifyGatewayFees();