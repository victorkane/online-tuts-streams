 <?php
/**
 * Plugin Name:       Meta Fields
 * Description:       Block description
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The Kinsta team
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
	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => 'meta_fields_metadata_block_render_callback',
		)
	);
}
add_action( 'init', 'meta_fields_metadata_block_block_init' );

/**
 * Render callback function.
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      Block instance.
 *
 * @return string The rendered output.
 */
function meta_fields_metadata_block_render_callback( $attributes, $content, $block ) {
	$post_meta = get_post_meta( get_the_ID() );
    
    $output = "";

    if( ! empty( $post_meta['_meta_fields_book_title'][0] ) ){
        $title = '<h3>' . esc_html( $post_meta['_meta_fields_book_title'][0] ) . '</h3>';
    } else {
        $title = '<h3>' . esc_attr( get_the_title() ) . '</h3>';
    }

    if( ! empty( $post_meta['_meta_fields_book_author'][0] ) ){
        $output .= '<li>' . __( 'Book author: ' ) . esc_html( $post_meta['_meta_fields_book_author'][0] ) . '</li>';
    }
    if( ! empty( $post_meta['_meta_fields_book_publisher'][0] ) ){
        $output .= '<li>' . __( 'Book publisher: ' ) . esc_html( $post_meta['_meta_fields_book_publisher'][0] ) . '</li>';
    }
    if( ! empty( $post_meta['_meta_fields_book_date'][0] ) ){
        $date = date_create( esc_html( $post_meta['_meta_fields_book_date'][0] ) );
        $output .= '<li>' . __( 'Book date: ' ) . date_format( $date, "F d, Y" ) . '</li>';
    }

    if( strlen( $output ) > 0 ){
        return '<div ' . get_block_wrapper_attributes() . '>' . $title . '<ul>' . $output . '</ul></div>'; 
    } else {
        return '<strong>' . __( 'Sorry. No fields available here!' ) . '</strong>';
    }
}

// register metabox
function meta_fields_add_meta_box(){
    add_meta_box(
        'meta_fields_meta_box', 
        __( 'Book details' ), 
        'meta_fields_build_meta_box_callback', 
        'post', 
        'side',
        'default',
        // hide the metabox in Gutenberg
        array('__back_compat_meta_box' => true)
     );
}

// build metabox
function meta_fields_build_meta_box_callback( $post ){
      wp_nonce_field( 'meta_fields_save_meta_box_data', 'meta_fields_meta_box_nonce' );
      $author = get_post_meta( $post->ID, '_meta_fields_book_title', true );
      $author = get_post_meta( $post->ID, '_meta_fields_book_author', true );
      $publisher = get_post_meta( $post->ID, '_meta_fields_book_publisher', true );
      $date = get_post_meta( $post->ID, '_meta_fields_book_date', true );
      ?>
      <div class="inside">
          <p><strong>Title</strong></p>
          <p><input type="text" id="meta_fields_book_title" name="meta_fields_book_title" value="<?php echo esc_attr( $title ); ?>" /></p>  
          <p><strong>Author</strong></p>
          <p><input type="text" id="meta_fields_book_author" name="meta_fields_book_author" value="<?php echo esc_attr( $author ); ?>" /></p>
          <p><strong>Publisher</strong></p>
          <p><input type="text" id="meta_fields_book_publisher" name="meta_fields_book_publisher" value="<?php echo esc_attr( $publisher ); ?>" /></p>
          <p><strong>Date</strong></p>
          <p><input type="date" id="meta_fields_book_date" name="meta_fields_book_date" value="<?php echo esc_attr( $date ); ?>" /></p>
      </div>
      <?php
}
add_action( 'add_meta_boxes', 'meta_fields_add_meta_box' );

// save meta data
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
    if ( ! isset( $_POST['meta_fields_book_date'] ) )
        return;
    if ( ! isset( $_POST['meta_fields_book_publisher'] ) )
        return;
    
    $title = sanitize_text_field( $_POST['meta_fields_book_title'] );
    $author = sanitize_text_field( $_POST['meta_fields_book_author'] );
    $date = sanitize_text_field( $_POST['meta_fields_book_date'] );
    $publisher = sanitize_text_field( $_POST['meta_fields_book_publisher'] );

    update_post_meta( $post_id, '_meta_fields_book_title', $title );
    update_post_meta( $post_id, '_meta_fields_book_author', $author );
    update_post_meta( $post_id, '_meta_fields_book_date', $date );
    update_post_meta( $post_id, '_meta_fields_book_publisher', $publisher );
}
add_action( 'save_post', 'meta_fields_save_meta_box_data' );

/**
 * Register the custom meta fields
 */
function meta_fields_register_meta() {

    $metafields = [ '_meta_fields_book_title', '_meta_fields_book_author', '_meta_fields_book_date', '_meta_fields_book_publisher' ];

    foreach( $metafields as $metafield ){
        // Pass an empty string to register the meta key across all existing post types.
        register_post_meta( '', $metafield, array(
            'show_in_rest' => true,
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function() { 
                return current_user_can( 'edit_posts' );
            }
        ));
    }  
}
add_action( 'init', 'meta_fields_register_meta' );