<?php
/**
 * Boutique engine room
 *
 * @package boutique
 */



/*
 *
 * Language function
 *
 */



function silvercareone_lang_setup() {
    load_child_theme_textdomain( 'silvercare', get_stylesheet_directory() . '/languages' );
	
	add_image_size( 'menu-icon', 150, 150, true ); // (ritagliata)
}
add_action( 'after_setup_theme', 'silvercareone_lang_setup' );

/*
 *
 * Favicon function
 *
 */
function blog_favicon() {
echo '<link rel="Shortcut Icon" type="image/x-icon" href="'. get_stylesheet_directory_uri().'/img/favicon.ico" />';
echo '<link rel="icon" type="image/x-icon" href="'. get_stylesheet_directory_uri().'/img/favicon.ico" />';
}
add_action('wp_head', 'blog_favicon', 1);

/*
 *
 * Unhook default storefront function
 *
 */
function unhook_functions() {

	if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE !== 'it' ){
	remove_action( 'storefront_header', 'storefront_header_cart', 		60 );
	remove_action( 'after_setup_theme', 'custom_header_setup' );
	remove_action( 'after_setup_theme', 	'storefront_custom_header_setup', 50 );
	}
}
add_action('init','unhook_functions');




add_action( 'init', 'my_add_excerpts_to_pages' );
function my_add_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
}



add_action( 'after_setup_theme', 'remove_featured_images_from_child_theme', 11 ); 

function remove_featured_images_from_child_theme() {

    remove_theme_support( 'custom-header' );
}


add_action( 'init', 'silvercare_remove_storefront_handheld_footer_bar' );

function silvercare_remove_storefront_handheld_footer_bar() {
  remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999 );
}





add_action( 'widgets_init', 'silvercareone_widgets_init' );
function silvercareone_widgets_init()
{
register_sidebar( array (
'name' => __( 'Shop Widget Area', 'silvercare' ),
'id' => 'shop-widget-area',
	'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
) );

    register_sidebar( array (
'name' => __( 'Newsletter', 'silvercare' ),
'id' => 'newsletter',
	'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
) );


}
 



function lang_category_id($id, $cat){
  if(function_exists('icl_object_id')) {
    return icl_object_id($id,$cat,true);
  } else {
    return $id;
  }
}

function lang_object_ids($ids_array, $type) {
 if(function_exists('icl_object_id')) {
  $res = array();
  foreach ($ids_array as $id) {
   $xlat = icl_object_id($id,$type,false);
   if(!is_null($xlat)) $res[] = $xlat;
  }
  return $res;
 } else {
  return $ids_array;
 }
}

 


if ( ! function_exists( 'storefront_credit' ) ) {
	/**
	 * Display the theme credit
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_credit() {
		?>
		<div class="site-info">
			<?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ); ?>
			<?php if ( apply_filters( 'storefront_credit_link', true ) ) { ?>
			Spazzolificio Piave S.p.a. Via A.Palladio 5, 35019 Tombolo – Pd (Italia) <?php if ( function_exists ('wc_accepted_payment_methods') ) wc_accepted_payment_methods(); ?>
			<?php } ?>
		</div><!-- .site-info -->
		<?php
	}
}

// Include the Google Analytics Tracking Code (ga.js)
// @ https://developers.google.com/analytics/devguides/collection/gajs/
function google_analytics_tracking_code(){

	$propertyID = 'UA-56230442-1'; 

	?>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', '<?php echo $propertyID; ?>', 'auto');
		  ga('send', 'pageview');
		
		</script>
	<?php 
}

// include GA tracking code before the closing head tag
add_action('wp_head', 'google_analytics_tracking_code', 100);


if ( ! function_exists( 'shop_get_sidebar' ) ) {

	/**
	 * Get the shop sidebar template.
	 *
	 */
	function shop_get_sidebar() {
		wc_get_template( 'sidebar-shop.php' );
	}
}
if ( ! function_exists( 'woocommerce_get_sidebar' ) ) {

	/**
	 * Get the shop sidebar template.
	 *
	 */
	function woocommerce_get_sidebar() {
		wc_get_template( 'sidebar-shop.php' );
	}
}
add_action( 'woocommerce_sidebar', 'shop_get_sidebar', 11 );



/**************************CheckoutMail**********************************/



//add_action('woocommerce_checkout_process', 'customise_checkout_field_process');
 
function customise_checkout_field_process()
{
	// strcspn returns the length of the part that does not contain any integers. We compare that with the string length, and if they differ, then there must have been an integer.
	if (strcspn($_POST['some_field'], '0123456789') != strlen($_POST['some_field'])) wc_add_notice(__('Inserire il codice fiscale corretto', 'woocommerce') , 'error');
}
	



// Inserito da Alex - Test 16/09/15
// problema: gli sconti sulla spedizione vengono efettuati PRIMA del coupon, generando un ulteriore sconto non voluto.
// dovremmo generare la spedizione gratuita sul risultato del coupon.
// Il sistema utilizza inoltre il plugin: http://www.woothemes.com/products/table-rate-shipping/
// Codice tratto da: http://raison.co/woocommerce-shipping-calculated-coupon/
// da fare tests.

// WooCommerce Shipping Calculated after Coupon
add_filter( 'woocommerce_shipping_free_shipping_is_available', 'filter_shipping', 10, 2 );
function filter_shipping( $is_available, $package ) {
	if ( WC()->cart->prices_include_tax )
		$total = WC()->cart->cart_contents_total + array_sum( WC()->cart->taxes );
	else
		$total = WC()->cart->cart_contents_total;
	$total = $total - WC()->cart->get_total_discount();
	// You can hardcode the number or get the setting from the shipping method
	$shipping_settings = get_option('woocommerce_free_shipping_settings');
	$min_total = $shipping_settings['min_amount'] > 0 ? $shipping_settings['min_amount'] : 0;
	
	if ( 50 > $total ) {
		$is_available = false;
	}
	return $is_available;
}
// This basically recalculates totals after the discount has been added
add_action( 'woocommerce_calculate_totals', 'change_shipping_calc' );
function change_shipping_calc( $cart ) {
		$packages = WC()->cart->get_shipping_packages();
		// Calculate costs for passed packages
		$package_keys 		= array_keys( $packages );
		$package_keys_size 	= sizeof( $package_keys );
		for ( $i = 0; $i < $package_keys_size; $i ++ ) {
			unset( $packages[ $package_keys[ $i ] ]['rates'] );
			$package_hash   = 'wc_ship_' . md5( json_encode( $packages[ $package_keys[ $i ] ] ) );
			delete_transient( $package_hash );
		}
		// Calculate the Shipping
		$cart->calculate_shipping();
		// Trigger the fees API where developers can add fees to the cart
		$cart->calculate_fees();
		// Total up/round taxes and shipping taxes
		if ( $cart->round_at_subtotal ) {
			$cart->tax_total          = $cart->tax->get_tax_total( $cart->taxes );
			$cart->shipping_tax_total = $cart->tax->get_tax_total( $cart->shipping_taxes );
			$cart->taxes              = array_map( array( $cart->tax, 'round' ), $cart->taxes );
			$cart->shipping_taxes     = array_map( array( $cart->tax, 'round' ), $cart->shipping_taxes );
		} else {
			$cart->tax_total          = array_sum( $cart->taxes );
			$cart->shipping_tax_total = array_sum( $cart->shipping_taxes );
		}
		// VAT exemption done at this point - so all totals are correct before exemption
		if ( WC()->customer->is_vat_exempt() ) {
			$cart->remove_taxes();
		}
}
add_filter( 'woocommerce_table_rate_query_rates_args', 'filter_shipping_2', 10 );
	function filter_shipping_2( $arguments ) {
		if ( WC()->cart->prices_include_tax )
			$total = WC()->cart->cart_contents_total + array_sum( WC()->cart->taxes );
		else
			$total = WC()->cart->cart_contents_total;
		$total = $total - WC()->cart->get_total_discount();
		$arguments['price'] = $total;
		return $arguments;
	}
		add_filter('gettext', 'translate_text');
		add_filter('ngettext', 'translate_text');

		function translate_text($translated) {
		$translated = str_ireplace(' Advance bank transfer ', ' Bonifico bancario anticipato ', $translated);
		$translated = str_ireplace('Original Text', 'Your Replacment Text', $translated);
		return $translated;
    }



/**
 * Load the individual classes required by this theme
 */
require_once( 'inc/class-silvercare-design.php' );

require_once( 'inc/class-silvercare-checkout.php' );
require_once( 'inc/class-silvercare-customizer.php' );
require_once( 'inc/class-silvercare-template.php' );
require_once( 'inc/class-silvercare-integrations.php' );