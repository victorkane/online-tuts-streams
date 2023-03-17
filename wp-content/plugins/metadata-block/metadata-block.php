<?php
/**
 * Plugin Name:       Meta Fields
 * Description:       Block Description
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       metadata-block
 *
 * @package           meta-fields
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function meta_fields_metadata_block_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'meta_fields_metadata_block_block_init' );

// register meta box
function meta_fields_add_meta_box(){
	add_meta_box(
		'meta_fields_meta_box', 
		__( 'Book details' ), 
		'meta_fields_build_meta_box_callback', 
		'post',
		'side',
		'default',
		// hide the meta box in Gutenberg
		array('__back_compat_meta_box' => true)
	 );
}

// build meta box
function meta_fields_build_meta_box_callback( $post ){
	  wp_nonce_field( 'meta_fields_save_meta_box_data', 'meta_fields_meta_box_nonce' );
	  $title = get_post_meta( $post->ID, '_meta_fields_book_title', true );
	  $author = get_post_meta( $post->ID, '_meta_fields_book_author', true );
	  ?>
	  <div class="inside">
	  	  <p><strong>Title</strong></p>
		  <p><input type="text" id="meta_fields_book_title" name="meta_fields_book_title" value="<?php echo esc_attr( $title ); ?>" /></p>	
		  <p><strong>Author</strong></p>
		  <p><input type="text" id="meta_fields_book_author" name="meta_fields_book_author" value="<?php echo esc_attr( $author ); ?>" /></p>
	  </div>
	  <?php
}
add_action( 'add_meta_boxes', 'meta_fields_add_meta_box' );

// save metadata
function meta_fields_save_meta_box_data( $post_id ) {
	if ( ! isset( $_POST['meta_fields_meta_box_nonce'] ) )
		return;
	if ( ! wp_verify_nonce( $_POST['meta_fields_meta_box_nonce'], 'meta_fields_save_meta_box_data' ) )
		return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;
	if ( ! current_user_can( 'edit_post', $post_id ) )
		return;

	if ( ! isset( $_POST['meta_fields_book_title'] ) )
		return;
	if ( ! isset( $_POST['meta_fields_book_author'] ) )
		return;

	$title = sanitize_text_field( $_POST['meta_fields_book_title'] );
	$author = sanitize_text_field( $_POST['meta_fields_book_author'] );

	update_post_meta( $post_id, '_meta_fields_book_title', $title );
	update_post_meta( $post_id, '_meta_fields_book_author', $author );
}
add_action( 'save_post', 'meta_fields_save_meta_box_data' );