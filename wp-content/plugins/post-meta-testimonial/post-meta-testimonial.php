<?php
/**
 * Plugin Name:       Post Meta Testimonial
 * Description:       A block quote style testimonial that saves to post meta.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-meta-testimonial
 *
 * @package           tutorial
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function tutorial_post_meta_testimonial_block_init() {
	register_block_type( __DIR__ . '/build' );
  register_post_type(
	  'product',
	  array(
		  'labels'       => array(
			  'name'          => __( 'Products', 'tutorial' ),
			  'singular_name' => __( 'Product', 'tutorial' ),
		  ),
		  'public'       => true,
		  'has_archive'  => true,
		  'show_in_rest' => true,
		  'supports'     => array(
			  'title',
			  'editor',
			  'thumbnail',
			  'excerpt',
			  'custom-fields',
		  ),
	  )
  );
	register_post_meta(
	  'product',
	  'testimonial',
	  array(
		  'show_in_rest'       => true,
		  'single'             => true,
		  'type'               => 'string',
		  'sanitize_callback'  => 'wp_kses_post',
	  )
  );
}
add_action( 'init', 'tutorial_post_meta_testimonial_block_init' );
