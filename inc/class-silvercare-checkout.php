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
		add_filter( 'woocommerce_checkout_fields', array( $this,'my_filter_checkout_fields' ));
		add_action( 'woocommerce_checkout_shipping' ,array( $this,'my_extra_checkout_fields' ));
		add_action( 'woocommerce_checkout_update_order_meta', array( $this,'my_save_extra_checkout_fields' ));
		add_action( 'woocommerce_thankyou', array( $this,'my_display_order_data'));
		add_action( 'woocommerce_view_order', array( $this,'my_display_order_data'));
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this,'my_display_order_data_in_admin' ));
		add_filter(	'woocommerce_email_order_meta_keys', array( $this,'my_email_order_meta_keys'));
		add_filter(	'woocommerce_checkout_process', array( $this,'my_custom_checkout_field_process'));

	}
	
	// Aggiungo campo extra_field in fase checkout
function my_filter_checkout_fields($fields){
    $fields['extra_fields'] = array(
				'some_field' => array(
					'type' => 'text',
					'label'     => __('Tax code / VAT ID', 'silvercare'),
					'placeholder'   => _x('This information is required by Italian law ', 'placeholder', 'silvercare'),
					'required'  => true
                ),
            );

    return $fields;
}


// Visualizzo i field in fase checkout
function my_extra_checkout_fields(){ 

    $checkout = WC()->checkout(); ?>

    <div class="extra-fields">
    <?php 
    // Ciclo foreach per leggere array prima funzione
    foreach ( $checkout->checkout_fields['extra_fields'] as $key => $field ) : ?>

            <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

        <?php endforeach; ?>
    </div>

<?php }


		function my_custom_checkout_field_process() {


			$tax_id_field = $_POST['some_field'];

			// Check if set, if its not set add an error.


			if ( isset( $tax_id_field ) ) {

				$tax_id_field_lenght = strlen( $tax_id_field );

			    if ( $tax_id_field_lenght  < 16) {
					wc_add_notice( __( 'Please enter your tax code or VAT ID', 'silvercare' ), 'error' );
				}
			}
		}


// Salvataggio extra fields
function my_save_extra_checkout_fields( $order_id ){
    // Controllo di sicurezza
    $tax_id_field = $_POST['some_field'];
    if( isset( $tax_id_field ) ) {
        update_post_meta( $order_id, '_some_field', sanitize_text_field( $_POST['some_field'] ) );
    }

}


// visualizzazione campo extra su ordine ricevuto o pagina mio account
function my_display_order_data( $order_id ){  ?>
    <table class="shop_table shop_table_responsive additional_info">
        <tbody>
            <tr>
                <th><?php _e( 'Tax code / VAT ID', 'silvercare' ); ?></th>
                <td><?php echo get_post_meta( $order_id, '_some_field', true ); ?></td>
            </tr>
            
        </tbody>
    </table>
<?php }



// Visualizzazione field in campo admin
function my_display_order_data_in_admin( $order ){  ?>
    <div class="order_data_column">
        <h4><?php _e( 'Extra fields', 'silvercare' ); ?></h4>
        <?php 
            echo '<p><strong>' . __( 'Tax code / VAT ID', 'silvercare' ) . ':</strong>' . get_post_meta( $order->id, '_some_field', true ) . '</p>';
            ?>
    </div>
<?php }





function my_email_order_meta_keys( $keys ) {

    $keys['C.F. / P.IVA'] = '_some_field';
	echo "<br/>";
	echo "<h2>" . _e( 'Extra fields', 'silvercare' ) . '</h2>';
echo "<br/>";
    return $keys;

}



	
}
}
return new Silvercare_checkout();