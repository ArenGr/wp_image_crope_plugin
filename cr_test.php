<?php

/*
Plugin Name: cr-test
 */


/* add_action('wp_insert_comment', 'cr_some_func', 10, 2); */

/* function cr_some_func($id, $comment) */
/* { */
/*         var_dump($comment->comment_author);exit(); */
/* } */

/* add_action('wp_head', 'cr_some_func', 10); */
/* function cr_some_func() */
/* { */
/*     echo '<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>'; */
/* } */

add_action( 'wp_enqueue_scripts', 'cr_included_libs' );
function cr_included_libs() {
	// Styles
	wp_enqueue_style( 'bootstrap-style', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), '4.3.1' );
	/* wp_enqueue_style( 'cropper-style', 'http://jcrop-cdn.tapmodo.com/v0.9.12/css/jquery.Jcrop.css', array(), 'v0.9.12'); */
	// Scripts
	wp_enqueue_script( 'popper-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array( 'jquery' ), '1.14.7', true );
	wp_enqueue_script( 'bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array( 'jquery' ), '4.3.1', true );
	wp_enqueue_script( 'ajax', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', array( 'jquery' ), '3.5.1', true );
	/* wp_enqueue_script( 'cropper-js', 'http://jcrop-cdn.tapmodo.com/v0.9.12/js/jquery.Jcrop.min.js', array( 'jquery' ), 'v0.9.12', true ); */
    if (!is_admin()) {
        $script_url = plugins_url( '/js/script.js', __FILE__ );
        wp_enqueue_script('custom-script', $script_url, array('jquery'), '3.5.1', true );
    }
}

add_action('wp_head', 'cr_add_comment_enctype');
function cr_add_comment_enctype()
{
	echo "<script type=\"text/javascript\">
		jQuery(document).ready(function(){
			jQuery('#commentform')[0].encoding = 'multipart/form-data'; });
		 </script>";
}

add_filter('comment_form_default_fields', 'cr_add_image_field');
function cr_add_image_field($fields) {
    $fields[ 'image' ] = '<p class="comment-form-image"><input type="file" id="image" name="image" onchange="cropImage()" multiple="false"></p>'.
    '<div id="mod" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <p>This is a simple Bootstrap modal. Click the "Cancel button", "cross icon" or "dark gray area" to close or hide the modal.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>';
	return $fields;
}

add_action( 'comment_post', 'save_extend_comment_meta_data' );
function save_extend_comment_meta_data( $comment_id ) {
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$attachment_id = media_handle_upload( 'image', $comment_id);

	if ( is_wp_error( $attachment_id ) ) {
		echo "";
	} else {
		echo "";
	}
}



add_filter( 'comment_text', 'customizing_comment_text', 20, 3 );
function customizing_comment_text( $comment_text, $comment, $args ) {
    $comment_id = $comment->comment_ID;
    $children = get_children( array(
        'post_parent'   => $comment_id,
        'post_type'=>'attachment'
    ));
    foreach ($children as $child) {
        $path = $child->guid;
    }
    if( $comment->comment_type === 'comment' ) {
        $comment_text = $comment_text . '<p><img src="'.$path.'" width="50px"/></p>';
    }
    return $comment_text;
}

