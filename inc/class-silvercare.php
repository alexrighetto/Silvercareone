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
		add_action( 'wp_enqueue_scripts',  array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts',  array( $this, 'enqueue_child_styles' ), 99 );
		add_action( 'init',                array( $this, 'unhook_functions' ) );
		add_action( 'storefront_header',   array( $this, 'silvercare_wpml_switcher'), 69 );
		add_action( 'after_setup_theme',   array( $this, 'silvercare_lang_setup' ) );
		add_action( 'init',                array( $this, 'silvercare_excerpts_to_pages' ) );
		add_action( 'send_headers',        array( $this, 'add_header_xua' ));
		add_action( 'template_redirect',   array( $this, 'unhook_thematic_functions' ) );
		//add_action( 'widgets_init',        array( $this,'theme_slug_widgets_init' ));
        add_action( 'send_headers',        array( $this,'add_header_xua' ));
        add_action( 'init',                array( $this,'jk_remove_storefront_handheld_footer_bar' ));
	}

	/**
	 * Enqueue Storefront Styles
	 * @return void
	 */
	public function enqueue_styles() {
		global $storefront_version;

		wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css', $storefront_version );
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

		wp_enqueue_style( 'Hind', '//fonts.googleapis.com/css?family=Hind:400,700', array( 'storefront-style' ) );
		
	}
	
	// Unhook default Thematic functions
	public function unhook_functions() {
		//remove_action( 'storefront_before_content',	'storefront_header_widget_region',	10 );
		remove_action( 'storefront_header', 'storefront_secondary_navigation',		30 );
        remove_action( 'storefront_page add_action', 'storefront_page_header', 10); 
        if ( defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE !== 'it' ){
			remove_action( 'storefront_header', 'storefront_header_cart', 		60 );
			remove_action( 'after_setup_theme', 'custom_header_setup' );
			remove_action( 'after_setup_theme', 	'storefront_custom_header_setup', 50 );
		}
	}
	
	public function silvercare_wpml_switcher(){
		do_action( 'icl_language_selector' );		
	}
	
    public function add_header_xua() {
        if ( ! is_admin() ) {
            header( 'X-UA-Compatible: IE=edge,chrome=1' );
        }
    }
	
	function theme_slug_widgets_init() {
		register_sidebar( array(
			'name' => __( 'Main Sidebar', 'theme-slug' ),
			'id' => 'sidebar-13',
			'description' => __( 'Widgets in this area will be shown on all posts and pages.', 'theme-slug' ),
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>',
		) );
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
    
    

    function jk_remove_storefront_handheld_footer_bar() {
      remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999 );
    }

	}

}

return new Silvercare_design();