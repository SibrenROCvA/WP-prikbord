<?php
/**
 * Prikbord Theme functions and definitions
 *
 * @package Prikbord
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue styles
 */
function prikbord_enqueue_styles() {
    // Parent theme style
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    
    // Child theme style
    wp_enqueue_style( 'prikbord-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'parent-style' ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'prikbord_enqueue_styles' );

/**
 * Add custom theme setup
 */
function prikbord_theme_setup() {
    // Add theme support features if needed
    // add_theme_support( 'custom-logo' );
    // add_theme_support( 'custom-header' );
    
    // Register navigation menus if needed
    // register_nav_menus( array(
    //     'primary' => __( 'Primary Menu', 'prikbord' ),
    // ) );
}
add_action( 'after_setup_theme', 'prikbord_theme_setup' );

/**
 * Customize theme options
 */
function prikbord_customize_register( $wp_customize ) {
    // Add customizer options specific to the Prikbord theme if needed
}
add_action( 'customize_register', 'prikbord_customize_register' );

/**
 * Add editor styles
 */
function prikbord_add_editor_styles() {
    // Add editor styles if needed
    // add_editor_style( 'editor-style.css' );
}
add_action( 'admin_init', 'prikbord_add_editor_styles' );
