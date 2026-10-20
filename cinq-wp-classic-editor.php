<?php
/**
 * Plugin Name: CINQ Classic Editor
 * Plugin URI: https://github.com/agencecinq/cinq-wp-classic-editor
 * Description: Turns the block editor off for every post type and widget, and stops loading block styles on the front end. No settings screen.
 * Version: 1.0.0
 * Author: CINQ
 * Author URI: https://agencecinq.com/
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: cinq-wp-classic-editor
 *
 * @package CinqWpClassicEditor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove the block stylesheets from the front end.
 *
 * Dequeued rather than unhooked: `wp_common_block_scripts_and_styles()` also
 * fires `enqueue_block_assets`, which other plugins may rely on, and
 * `classic-theme-styles` is enqueued from both hooks.
 *
 * @return void
 */
function cinq_wp_classic_editor_dequeue_block_styles(): void {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
}

add_filter( 'use_block_editor_for_post_type', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false' );

/*
 * Bundle core block styles into `wp-block-library` instead of loading each
 * block's stylesheet on render (the default for classic themes since 6.9),
 * so the one stylesheet left can be dequeued.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_false' );

add_action( 'wp_enqueue_scripts', 'cinq_wp_classic_editor_dequeue_block_styles', 11 );

remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
