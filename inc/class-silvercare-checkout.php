<?php
/**
 * Silvercare Class
 *
 * @author   WooThemes
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Silvercare_checkout' ) ) {

class Silvercare_checkout {
	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_checkout_fields', array( $this, 'silvercare_filter_checkout_fields' ) );
		add_action( 'woocommerce_checkout_shipping', array( $this,'silvercare_extra_checkout_fields' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this,'silvercare_save_extra_checkout_fields', 10, 2 ) );
		add_filter('woocommerce_email_order_meta_keys', array( $this,'silvercare_email_order_meta_keys') );
		

	}
	
	public function silvercare_filter_checkout_fields($fields){
		$fields['extra_fields'] = array(
				'some_field' => array(
				'type' => 'text',
				'label'     => __('Codice Fiscale / P.IVA', 'silvercare'),
				'placeholder'   => _x('Codice Fiscale / P.IVA', 'placeholder', 'silvercare'),
				'required'  => true
					),
				);
		return $fields;
	}
	// Visualizzo i field in fase checkout
	public function silvercare_extra_checkout_fields(){ 

		$checkout = WC()->checkout(); ?>

		<div class="extra-fields">
		<?php 
		// Ciclo foreach per leggere array prima funzione
		foreach ( $checkout->checkout_fields['extra_fields'] as $key => $field ) : ?>

				<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

			<?php endforeach; ?>
		</div>

	<?php }
	
	
	public function silvercare_save_extra_checkout_fields( $order_id, $posted ){
		// Controllo di sicurezza
		if( isset( $posted['some_field'] ) ) {
			update_post_meta( $order_id, '_some_field', sanitize_text_field( $posted['some_field'] ) );
		}
		if( isset( $posted['another_field'] ) && in_array( $posted['another_field'], array( 'a', 'b', 'c' ) ) ) {
			update_post_meta( $order_id, '_another_field', $posted['another_field'] );
		}
	}
	
	function silvercare_email_order_meta_keys( $keys ) {

		$keys['C.F. / P.IVA'] = '_some_field';
		echo "<br/>";
		echo "<h2>Campi Extra";
		echo "<br/>";
		return $keys;

	}
}
}
return new Silvercare_checkout();