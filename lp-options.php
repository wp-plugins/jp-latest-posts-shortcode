<?php

// Default options values
$lp_options = array(
	'feat_img_size' => '50',
	'posts_count' => '1',
	'featured_image' => true,
	'post_info' => true
);

if ( is_admin() ) : // Load only if we are viewing an admin page

function lp_register_settings() {
	// Register settings and call sanitation functions
	register_setting( 'lp_theme_options', 'lp_options', 'lp_validate_options' );
}

add_action( 'admin_init', 'lp_register_settings' );


function lp_theme_options() {
	// Add theme options page to the addmin menu
	add_options_page( 'JP Latest Posts', 'JP Latest Posts', 'manage_options', 'latest_posts_settings', 'lp_theme_options_page' );
}

add_action( 'admin_menu', 'lp_theme_options' );

// Function to generate options page
function lp_theme_options_page() {
	global $lp_options, $lp_categories, $lp_layouts;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>

	<div class="wrap">

	<?php screen_icon(); echo "<h2>JP Latest Post Options</h2>";
	// This shows the page's name and an icon if one has been provided ?>

	<?php if ( false !== $_REQUEST['updated'] ) : ?>
	<div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
	<?php endif; // If the form has just been submitted, this shows the notification ?>

	<form method="post" action="options.php">

	<?php $settings = get_option( 'lp_options', $lp_options ); ?>
	
	<?php settings_fields( 'lp_theme_options' );
	/* This function outputs some hidden fields required by the form,
	including a nonce, a unique number used to ensure the form has been submitted from the admin page
	and not somewhere else, very important for security */ ?>

	<table class="form-table"><!-- Grab a hot cup of coffee, yes we're using tables! -->
	
	<tr valign="top"><th scope="row"><label for="posts_count">Show Post</label></th>
	<td>
	<input id="posts_count" name="lp_options[posts_count]" type="text" value="<?php  esc_attr_e($settings['posts_count']); ?>" />
	<label for="posts_count">Chose how many latest posts will show from each category.</label>
	</td>
	</tr>
	
	<tr valign="top"><th scope="row"><label for="feat_img_size">Featured Image Size</label></th>
	<td>
	<input id="feat_img_size" name="lp_options[feat_img_size]" type="text" value="<?php  esc_attr_e($settings['feat_img_size']); ?>" />
	<label for="feat_img_size">Default featured image size 50x50.Input your size by px.Numeric only.</label>
	</td>
	</tr>
	
	<tr valign="top"><th scope="row">Featured Image</th>
	<td>
	<input type="checkbox" id="featured_image" name="lp_options[featured_image]" value="1" <?php checked( true, $settings['featured_image'] ); ?> />
	<label for="featured_image">Show featured image.</label>
	</td>
	</tr>
	
	<tr valign="top"><th scope="row">Post Meta</th>
	<td>
	<input type="checkbox" id="post_info" name="lp_options[post_info]" value="1" <?php checked( true, $settings['post_info'] ); ?> />
	<label for="post_info">Show post Meta under title.</label>
	</td>
	</tr>

	</table>

	<p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>

	</form>

	</div>

	<?php
}

function lp_validate_options( $input ) {
	global $lp_options, $lp_categories, $lp_layouts;

	$settings = get_option( 'lp_options', $lp_options );
	
	// We strip all tags from the text field, to avoid vulnerablilties like XSS
	$input['posts_count'] = wp_filter_nohtml_kses( $input['posts_count'] );
	
	// We strip all tags from the text field, to avoid vulnerablilties like XSS
	$input['feat_img_size'] = wp_filter_nohtml_kses( $input['feat_img_size'] );
	
	// If the checkbox has not been checked, we void it
	if ( ! isset( $input['post_info'] ) )
		$input['post_info'] = null;
	// We verify if the input is a boolean value
	$input['post_info'] = ( $input['post_info'] == 1 ? 1 : 0 );
	
	// If the checkbox has not been checked, we void it
	if ( ! isset( $input['featured_image'] ) )
		$input['featured_image'] = null;
	// We verify if the input is a boolean value
	$input['featured_image'] = ( $input['featured_image'] == 1 ? 1 : 0 );
	
	return $input;
}

endif;  // EndIf is_admin()