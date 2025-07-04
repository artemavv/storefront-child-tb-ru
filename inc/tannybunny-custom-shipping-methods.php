<?php

if ( !defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'From_USA_Shipping_Method' ) ) {

	class From_USA_Shipping_Method extends WC_Shipping_Method {

		public function __construct() {
			
			$this->id = 'tb_usa_shipping';
			$this->method_title = __( 'Shipping from USA', 'woocommerce' );
			$this->method_description = __( 'Custom Shipping Method for TannyBunny', 'woocommerce' );
			
			//$this->availability = 'including'; 
			
			
			$this->availability = 'excluding';
			$this->countries = array();
			//$this->countries = TannyBunny_Custom_Shipping_Helper::free_shipping_countries( 'us' );
			
			$this->init();
			
			$this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
			$this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Shipping', 'woocommerce' );
			$this->title_free = isset( $this->settings['title_free'] ) ? $this->settings['title_free'] : __( 'Free Shipping', 'woocommerce' );
			$this->title_express = isset( $this->settings['title_express'] ) ? $this->settings['title_express'] : __( 'Express Shipping', 'woocommerce' );
		}

		/**
		 * Init your settings 
		 * 
		 * @access public 
		 * @return void 
		 */
		function init() {
			// Load the settings API 
			$this->init_form_fields();
			$this->init_settings();
			// Save settings in admin if you have any defined 
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options') );

		}

		/**
		 * Define settings field for this shipping 
		 * @return void 
		 */
		function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable', 'woocommerce' ),
					'type' => 'checkbox',
					'description' => __( 'Enable this shipping method.', 'woocommerce' ),
					'default' => 'yes'
				),
				'title' => array(
					'title' => __( 'Title', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Title to be displayed on checkout', 'woocommerce' ),
					'default' => __( 'From USA', 'woocommerce' )
				),
				'title_free' => array(
					'title' => __( 'Title for Free delivery', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Title to be displayed on checkout', 'woocommerce' ),
					'default' => __( 'From USA - Free', 'woocommerce' )
				),
				'title_express' => array(
					'title' => __( 'Title for Express delivery', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Title to be displayed on checkout', 'woocommerce' ),
					'default' => __( 'From USA - Express', 'woocommerce' )
				)
			);
		}

		/**
		 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters. 
		 * 
		 * @access public 
		 * @param array $package 
		 * @return void 
		 */
		public function calculate_shipping( $package = [] ) {

			$cost = 0;
			$wc_product = false;
			
			$delivery_from_usa = true;
			
			foreach ( $package['contents'] as $item_id => $values ) {
				$wc_product = new WC_Product( $values['product_id'] );
				
				$warehouse_names = array_filter( array_map( 'trim', explode( ',' , $wc_product->get_attribute( 'warehouse' ) ) ) );
				$available_warehouses      = TannyBunny_Custom_Shipping_Helper::find_warehouses_by_names( $warehouse_names );
				
				if ( ! array_key_exists( 'us', $available_warehouses ) ) { // this product is not available in US warehouse
					$delivery_from_usa = false;
					break;
				}
			}
			
			if ( $delivery_from_usa && $wc_product ) {
			
				$country = $package["destination"]["country"];
				
				$shipping = new TannyBunny_Custom_Shipping_Helper( $wc_product, $country );
				$cost = $shipping->get_delivery_cost( 'standard', 'us' );
				
        // if a country is not in "FREE DELIVERY" list, make the standard delivery unavailable (when shipping from USA) 
        
				if ( ! $shipping->is_standard_shipping_available() ) {
					$cost = -1;
				}
        
				if ( $cost >= 0 ) { // negative value indicates inavailable delivery
					
					$dates = $shipping->get_delivery_date_estimate( 'standard', 'us' );
					
					$label = $this->title . ' (Arrives: ' . $dates . ')';
					
					if ( $cost == 0 ) {
						$label = $this->title_free . ' (Arrives: ' . $dates . ')';
					}
					
					$rate = array(
						'id' => $this->id,
						'label' => $label,
						'cost' => $cost
					);
					$this->add_rate( $rate );
				}
				
				$express_cost = $shipping->get_delivery_cost( 'express', 'us' );
				
				if ( $express_cost >= 0 ) { // negative value indicates inavailable delivery
					
					$dates = $shipping->get_delivery_date_estimate( 'express', 'us' );
					$rate = array(
						'id' => $this->id . '_express',
						'label' => $this->title_express . ' (Arrives: ' . $dates . ')',
						'cost' => $express_cost
					);
					$this->add_rate( $rate );
				}
			}			
		}
	}

}


if ( ! class_exists( 'From_Armenia_Shipping_Method' ) ) {

	class From_Armenia_Shipping_Method extends WC_Shipping_Method {

		public function __construct() {
			
			$this->id = 'tb_am_shipping';
			$this->method_title = __( 'Shipping from Armenia', 'woocommerce' );
			$this->method_description = __( 'Custom Shipping Method for TannyBunny', 'woocommerce' );
			
			//$this->availability = 'including'; 
			
			// exclude countries where free shipping is available
			$this->availability = 'excluding';
			$this->countries = array();
			
			$this->init();
			
			$this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
			$this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Shipping', 'woocommerce' );
			$this->title_free = isset( $this->settings['title_free'] ) ? $this->settings['title_free'] : __( 'Free Shipping', 'woocommerce' );
			$this->title_express = isset( $this->settings['title_express'] ) ? $this->settings['title_express'] : __( 'Express Shipping', 'woocommerce' );
		}

		/**
		 * Init your settings 
		 * 
		 * @access public 
		 * @return void 
		 */
		function init() {
			// Load the settings API 
			$this->init_form_fields();
			$this->init_settings();
			// Save settings in admin if you have any defined 
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options') );

		}

		/**
		 * Define settings field for this shipping 
		 * @return void 
		 */
		function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable', 'woocommerce' ),
					'type' => 'checkbox',
					'description' => __( 'Enable this shipping method.', 'woocommerce' ),
					'default' => 'yes'
				),
				'title' => array(
					'title' => __( 'Title', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Title to be displayed on checkout', 'woocommerce' ),
					'default' => __( 'From Armenia', 'woocommerce' )
				),
				'title_free' => array(
					'title' => __( 'Title for Free delivery', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Title to be displayed on checkout', 'woocommerce' ),
					'default' => __( 'From Armenia - Free', 'woocommerce' )
				),
				'title_express' => array(
					'title' => __( 'Title for Express delivery', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Title to be displayed on checkout', 'woocommerce' ),
					'default' => __( 'From Armenia - Express', 'woocommerce' )
				)
			);
		}

		/**
		 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters. 
		 * 
		 * @access public 
		 * @param array $package 
		 * @return void 
		 */
		public function calculate_shipping( $package = [] ) {

			$cost = 0;
			$wc_product = false;
			
			$delivery_from_armenia = true;
			
			foreach ( $package['contents'] as $item_id => $values ) {
				$wc_product = new WC_Product( $values['product_id'] );
				
				$warehouse_names = array_filter( array_map( 'trim', explode( ',' , $wc_product->get_attribute( 'warehouse' ) ) ) );
				$available_warehouses      = TannyBunny_Custom_Shipping_Helper::find_warehouses_by_names( $warehouse_names );
				
				if ( count($available_warehouses) &&  ! array_key_exists( 'am', $available_warehouses ) ) { // this product is not available in AM warehouse
					$delivery_from_armenia = false;
					break;
				}
			}
			
			if ( $delivery_from_armenia && $wc_product ) {
			
				$country = $package["destination"]["country"];
				
				$shipping = new TannyBunny_Custom_Shipping_Helper( $wc_product, $country );
				$cost = $shipping->get_delivery_cost( 'standard', 'am' );
				
				if ( ! $shipping->is_standard_shipping_available() ) {
					$cost = -1;
				}
				
				tb_log(" TannyBunny_Custom_Shipping_Helper $cost");
				
				if ( $cost >= 0 ) { // negative value indicates inavailable delivery
					
					$dates = $shipping->get_delivery_date_estimate( 'standard', 'am' );
					
					$label = $this->title . ' (Arrives: ' . $dates . ')';
					
					if ( $cost == 0 ) {
						$label = $this->title_free . ' (Arrives: ' . $dates . ')';
					}
					
					$rate = array(
						'id' => $this->id,
						'label' => $label,
						'cost' => $cost
					);
					$this->add_rate( $rate );
				}
				
				$express_cost = $shipping->get_delivery_cost( 'express', 'am' );
				
				if ( $express_cost >= 0 ) { // negative value indicates inavailable delivery
					
					$dates = $shipping->get_delivery_date_estimate( 'express', 'am' );
					$rate = array(
						'id' => $this->id . '_express',
						'label' => $this->title_express . ' (Arrives: ' . $dates . ')',
						'cost' => $express_cost
					);
					$this->add_rate( $rate );
				}
			}			
		}
	}

}



function add_tannybunny_shipping_methods( $methods ) {
	$methods['tb_usa_shipping'] = 'From_USA_Shipping_Method';
	$methods['tb_armenia_shipping'] = 'From_Armenia_Shipping_Method';
	return $methods;
}

add_filter( 'woocommerce_shipping_methods', 'add_tannybunny_shipping_methods' );

function fedex_tannybunny_validate_order( $posted ) {
	$packages = WC()->shipping->get_packages();
	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

	if ( is_array( $chosen_methods ) && in_array( 'tb_fedex_shipping', $chosen_methods ) ) {

		foreach ( $packages as $i => $package ) {
			
			if ( $chosen_methods[$i] != "tb_fedex_shipping" ) {
				continue;
			}
			
			$Fedex_Shipping_Method = new FedEx_TannyBunny_Shipping_Method();
			$weightLimit = (int) $Fedex_Shipping_Method->settings['weight'];
			$weight = 0;
			foreach ( $package['contents'] as $item_id => $values ) {
				$_product = $values['data'];
				$weight = 123; //$weight + $_product->get_weight() * $values['quantity'];
			}
			$weight = wc_get_weight( $weight, 'kg' );

			if ( $weight > $weightLimit ) {
				$message = sprintf( __( 'Sorry, %d kg exceeds the maximum weight of %d kg for %s', 'tutsplus' ), $weight, $weightLimit, $Fedex_Shipping_Method->title );

				$messageType = "error";
				if ( !wc_has_notice( $message, $messageType ) ) {

					wc_add_notice( $message, $messageType );
				}
			}
		}
	}
}

//add_action( 'woocommerce_review_order_before_cart_contents', 'fedex_tannybunny_validate_order', 10 );
//add_action( 'woocommerce_after_checkout_validation', 'fedex_tannybunny_validate_order', 10 );
