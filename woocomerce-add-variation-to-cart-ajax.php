<?php
/*
Plugin Name: WooCommerce Add Variation to Cart Ajax
Plugin URI: http://nathanielthomas.net
Description: Allow the addition of variation products to the cart via ajax in WooCommerce
Version: 1.0
Author: Nathaniel Thomas
Author URI: http://www.nathanielthomas.net
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

class WC_VAC {
	
	public $plugin_path;
	public $plugin_url;

	function __construct(){

		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_url  = plugin_dir_url( __FILE__ );

		add_action( 'init' , array( $this, 'load_classes' ) );

	}

	function load_classes(){
		require_once($this->plugin_path . 'includes/class-wc-vac-ajax.php');
	}

}

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	new WC_VAC;
}