<?php

class WC_VAC_Ajax extends WC_AJAX {
	
	function __construct(){

		// woocommerce_EVENT => nopriv
		$ajax_events = array(
			'remove_from_cart'              => true,
			'add_variation_product_to_cart' => true,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
		
			add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( $this, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( $this, $ajax_event ) );
			}
		}

	}

	/**
	 * Remove from cart
	 */
	
	public function remove_from_cart() { 
		     
		$cart_item_key     = $_POST['remove_item'];
		
		// var_dump( $cart_item_key );           
		
		WC()->cart->set_quantity( $cart_item_key, 0, true );

		// Return fragments
		$this->get_refreshed_fragments();

		die();
	}

	/**
	 * AJAX add variation product to cart
	 */
	public function add_variation_product_to_cart() {
		ob_start();

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['product_id'] ) );
		$quantity          = empty( $_REQUEST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_REQUEST['quantity'] );
		$variation_id      = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['variation_id'] ) );
		$variation         = $_REQUEST['variation'];
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		$add_to_cart = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );
		
		if ( $passed_validation && $add_to_cart ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				wc_add_to_cart_message( $product_id );
			}

			// Return fragments
			$this->get_refreshed_fragments();

		} else {

			$messages = wc_get_notices();
			$error_messages = Array();
			
			if( isset($messages['error']) ){ 
				foreach($messages['error'] as $message){
					$error_message    = str_replace('<a href="'.get_bloginfo('wpurl').'" class="button wc-forward">View Cart</a>', ' ', $message); 
					$error_messages[] = $error_message;
				}
			}

			// If there was an error adding to the cart, redirect to the product page to show any errors
			$data = array(
				'error' => true,
				'messages' => $error_messages ? $error_messages : null,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
			);

			//echo json_encode( $data );
			wp_send_json( $data );
		}

		die();
	}

}

new WC_VAC_Ajax;