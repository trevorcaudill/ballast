<?php
/**
 * Setup File
 * 
 * @package tcaudill\Ballast
 * @since 1.0.0
 * @author Trevor Caudill
 * @link https://trevorcaudill.com
 * @license GNU General Public License 2.0+
 */
namespace tcaudill\Ballast;

add_action('genesis_setup', __NAMESPACE__ . '\setup_child_theme', 15);
/**
 * Setup child theme.
 * 
 * @since 1.0.0
 * 
 * @return void
 */
function setup_child_theme() {

    load_child_theme_textdomain( CHILD_TEXT_DOMAIN, apply_filters( 'child_theme_textdomain', CHILD_THEME_DIR . '/languages', CHILD_TEXT_DOMAIN ) );

    unregister_genesis_callbacks();

    add_new_image_sizes();
    add_theme_supports();

}
/**
 * Unregister Genesis callbacks.  We do this here because the child theme loads before Genesis.
 *
 * @since 1.0.0
 *
 * @return void
 */
function unregister_genesis_callbacks() {
    unregister_menu_callbacks();
    unregister_sidebar_callbacks();
}
/**
 * Setup theme support.
 * 
 * @since 1.0.0
 * 
 * @return void
 */
function add_theme_supports() {
    $config = array(
        'html5' => array( 
            'caption', 
            'comment-form', 
            'comment-list', 
            'gallery', 
            'search-form' ),
        'genesis-structural-wraps' => array( 
            'header',
            // 'menu-primary',
            'menu-secondary',
            // 'site-inner',
            'footer-widgets',
            'footer' ),
        'genesis-accessibility' => array( 
            '404-page', 
            'drop-down-menu', 
            'headings', 
            'rems', 
            'search-form', 
            'skip-links' ),
        'genesis-responsive-viewport' => null,
        'custom-header' => array(
            'width'           => 600,
            'height'          => 160,
            'header-selector' => '.site-title a',
            'header-text'     => false,
            'flex-height'     => true,
        ),
        'custom-background' => null,
        'genesis-after-entry-widget-area' => null,
        'genesis-footer-widgets' => '3',
        'genesis-menus' => array( 
            'primary' => __( 'Primary Menu', CHILD_TEXT_DOMAIN ), 
            'secondary' => __( 'Footer Menu', CHILD_TEXT_DOMAIN ) 
        ),
    );

    foreach( $config as $feature => $args ) {
        add_theme_support( $feature, $args );
    }
}

/**
 * Adds New Image Sizes.
 * 
 * @since 1.0.0
 * 
 * @return void
 */
function add_new_image_sizes() {
    $config = array(
        'featured-image' => array(
            'width' => 720,
            'height' => 400,
            'crop' => TRUE,
        ),
    );

    foreach($config as $name => $args ) {
        $crop = array_key_exists( 'crop', $args ) ? $args['crop'] : false;
        
        add_image_size( $name, $args['width'], $args['height'], $crop);
    }

}

add_filter( 'genesis_theme_settings_defaults', __NAMESPACE__ . '\setting_theme_defaults' );
/**
 * Sets the theme setting defaults.
 * 
 * @since 1.0.0
 * 
 * @return void
 */
function setting_theme_defaults( array $defaults) {
    $config = get_theme_settings_defaults();
    $defaults = wp_parse_args($config, $defaults);

    return $defaults;
}

add_action( 'after_switch_theme', __NAMESPACE__ . '\update_theme_settings_defaults' );
/**
 * Updates the theme setting defaults.
 * 
 * @since 1.0.0
 * 
 * @return void
 */
function update_theme_settings_defaults() {

    $config = get_theme_settings_defaults();

    if ( function_exists( 'genesis_update_settings' ) ) {
       
        genesis_update_settings($config);
        
    } 

    update_option( 'posts_per_page', $config['blog_cat_num'] );
}
/**
 * Get the theme setting defaults.
 * 
 * @since 1.0.0
 * 
 * @return void
 */
function get_theme_settings_defaults() {
    return array(
        'blog_cat_num'              => 12,	
        'content_archive'           => 'full',
        'content_archive_limit'     => 0,
        'content_archive_thumbnail' => 0,
        'posts_nav'                 => 'numeric',
        'site_layout'               => 'content-sidebar',
    );
}

/**
 * Remove Genesis Page Templates
 *
 * 
 * @since 1.1.0
 */
function ballast_remove_genesis_page_templates( $page_templates ) {
	unset( $page_templates['page_archive.php'] );
	unset( $page_templates['page_blog.php'] );
	return $page_templates;
}
add_filter( 'theme_page_templates', __NAMESPACE__ . '\ballast_remove_genesis_page_templates' );
