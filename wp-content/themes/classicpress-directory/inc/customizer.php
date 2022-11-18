<?php
/**
 * Theme Customizer
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param CP_Customize_Manager $cp_customize Theme Customizer object.
 */
function bedrock_customize_register( $cp_customize ) {
	$cp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$cp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$cp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $cp_customize->selective_refresh ) ) {
		$cp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'bedrock_customize_partial_blogname',
		) );
		$cp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'bedrock_customize_partial_blogdescription',
		) );
	}
}
add_action( 'customize_register', 'bedrock_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function bedrock_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function bedrock_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function bedrock_customize_preview_js() {
	wp_enqueue_script( 'bedrock-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20211103', true );
}
add_action( 'customize_preview_init', 'bedrock_customize_preview_js' );
