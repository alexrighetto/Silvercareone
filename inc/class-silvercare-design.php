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

if ( ! class_exists( 'Silvercare_design' ) ) {

class Silvercare_design {
	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 	'wp_enqueue_scripts', 	array( $this, 'enqueue_styles' ) );
		add_action( 	'wp_enqueue_scripts', 	array( $this, 'enqueue_child_styles' ), 99 );
		
		add_action( 	'wp_enqueue_scripts', 	array( $this, 'enqueue_scripts' ) );
		add_filter( 'body_class',                 array( $this, 'body_classes' ), 99 );
		add_action(		'init', 				array( $this, 'unhook_functions' ) );
		
		add_action( 	'after_setup_theme', 	array( $this, 'silvercare_lang_setup' ) );
		add_action( 	'init',	 				array( $this, 'silvercare_excerpts_to_pages' ) );
		add_action( 	'send_headers', array( $this, 'add_header_xua' ));
		add_action(		'template_redirect', array( $this, 'unhook_thematic_functions' ) );
		//add_action( 	'widgets_init', array( $this,'theme_slug_widgets_init' ));
		add_shortcode( 	'button', array( $this,'silvercare_button_shortcode' ));
		add_action( 	'storefront_footer', array( $this, 'add_social_icons'), 8);
		add_action( 'init' , 					array( $this, 'silvercare_register_ubermenu_skins') , 9 );
		
	}
	
	
	public function body_classes( $classes ) {
			
			// If our main sidebar doesn't contain widgets, adjust the layout to be full-width.
			if (  is_active_sidebar( 'shop-widget-area' ) ) {
				
				if (($key = array_search('storefront-full-width-content', $classes)) !== false) {
					unset($classes[$key]);
				}
			}
			
			return $classes;
		}
	
	/**
	 * Enqueue Storefront Styles
	 * @return void
	 */
	public function enqueue_styles() {
		global $storefront_version;

		wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css', $storefront_version );
	}
	
	function theme_slug_widgets_init() {
		register_sidebar( array(
			'name' => __( 'Main Sidebar', 'silvercare' ),
			'id' => 'sidebar-12',
			'description' => __( 'Widgets in this area will be shown on all posts and pages.', 'silvercare' ),
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>',
		) );
	}
	
	public function enqueue_scripts() {
		global $storefront_version, $silvercare_version;
	
	wp_enqueue_script(  'silvercare-script',  get_stylesheet_directory_uri() . '/assets/js/main.min.js',  array('jquery'), $silvercare_version, true );
		
	}
	
	/**
	 * Enqueue Storechild Styles
	 * @return void
	 */
	public function enqueue_child_styles() {
		global $storefront_version, $silvercare_version;

		/**
		 * Styles
		 */
		wp_style_add_data( 'storefront-child-style', 'rtl', 'replace' );

		wp_enqueue_style( 'Poppins', '//fonts.googleapis.com/css?family=Poppins:400,700', array( 'storefront-style' ) );
		
	}
	
	// Unhook default Thematic functions
	public function unhook_functions() {
		//remove_action( 'storefront_before_content',	'storefront_header_widget_region',	10 );
		remove_action( 'storefront_header', 'storefront_product_search', 40 );
		remove_action( 'storefront_header', 'storefront_site_branding', 20 );
		add_action( 'storefront_header', 'storefront_site_branding', 43 );
		
		
		//remove_action( 'storefront_header', 'storefront_secondary_navigation',		30 );
		
		if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE !== 'it' ){
			remove_action( 'storefront_header', 'storefront_header_cart', 		60 );
			remove_action( 'after_setup_theme', 'custom_header_setup' );
			remove_action( 'after_setup_theme', 	'storefront_custom_header_setup', 50 );
		}
	}
	
	public function silvercare_wpml_switcher(){
		do_action( 'icl_language_selector' );
		
	}
	
	public function silvercare_lang_setup() {
    load_child_theme_textdomain( 'storefront', get_stylesheet_directory() . '/languages' );
	}
	
	public function silvercare_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
	}
	public function add_header_xua() {
	if ( ! is_admin() ) {
		
	header( 'X-UA-Compatible: IE=edge,chrome=1' );
	
	}
	}
	function unhook_thematic_functions() {

		if(function_exists('is_woocommerce')){
			if ( is_woocommerce() ) {
				remove_action( 'woo_main_after', 'woocommerce_get_sidebar', 10); 
				remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
				remove_action( 'storefront_sidebar','storefront_get_sidebar',10 );
			}}
		}
	
	function silvercare_button_shortcode( $atts, $content = null ) {
	
	// Extract shortcode attributes
	extract( shortcode_atts( array(
		'href'    => '',
		'title'  => '',
		'target' => '',
		'text'   => '',
		'color'  => 'green',
	), $atts ) );

	// Use text value for items without content
	$content = $text ? $text : $content;

	// Return button with link
	if ( $href ) {

		$link_attr = array(
			'href'   => esc_url( $href ),
			'title'  => esc_attr( $title ),
			'target' => ( 'blank' == $target ) ? '_blank' : '',
			'class'  => 'button color-' . esc_attr( $color ),
		);

		$link_attrs_str = '';

		foreach ( $link_attr as $key => $val ) {

			if ( $val ) {

				$link_attrs_str .= ' '. $key .'="'. $val .'"';

			}

		}


		return '<a'. $link_attrs_str .'><span>'. do_shortcode( $content ) .'</span></a>';

	}

	// No link defined so return button as a span
	else {

		return '<span class="button"><span>'. do_shortcode( $content ) .'</span></span>';

	}

}

	function add_social_icons(){
		?>
		<section class="social_section">
			
			<div class='social_cta'>
				<p><?php _e('Follow us:', 'silvercare'); ?> </p><!--to do: translate;-->
			</div>
			<div class='all_icons'>
				<a href="https://www.youtube.com/channel/UC44ZpriQiv0liBc9lGjKz7w" alt="Youtube" target='_blank'><span class="fa fa-youtube-play"></span></a>
				<a href="https://www.facebook.com/silvercareitalia" alt="Facebook" target='_blank'><span class="fa fa-facebook"></span></a>
				<a href="ttps://plus.google.com/+Silvercareone" alt="Google Plus" target='_blank'><span class="fa fa-google-plus"></span></a>
				<a href="https://www.instagram.com/silvercareone" alt="Instagram" target='_blank'><span class="fa fa-instagram"></span></a>
			</div>
			
		</section>
		
		<?php
	}
	
	function silvercare_register_ubermenu_skins(){
		//wp_die( get_stylesheet_directory_uri() . '/asset/sass/ubermenu/ubermenu.css');
   if( function_exists( 'ubermenu_register_skin' ) ){
      $skin_slug = 'silvercare-maintheme';       //replace with your skin slug
      $skin_name = '[Silvercareone] Main theme';   //Replace with the name of your skin (visible to users)
      $skin_path = get_stylesheet_directory_uri() . '/assets/sass/ubermenu/ubermenu.css';  //Replace with path to the custom skin in your theme
      ubermenu_register_skin( $skin_slug , $skin_name , $skin_path );
   }
}
	

	}

}

return new Silvercare_design();