<?php
/**
 * Plugin Name: JP Latest Posts Shortcode
 * Plugin URI: http://www.skjoybd.com/plugins/latest-posts-shortcode
 * Description: This is a simple plugin for listing latest posts from your blog.Use shortcode [latest-posts] to post or page.Then see,a list of latest posts will appear to your desired post or page.
 * Version: 1.0
 * Author: Skjoy
 * Author URI: http://www.skjoybd.com
 * Requires at least: 3.0
 * Tested Up to: 3.8
 * Stable Tag: 2.0
 * License: GPL v2
 * Shortname: lp or lp_
 */

include_once( 'lp-options.php' );
 
 // Creating main function for latest posts
 
function jp_latest_posts_shortcode() { ?>
	<!-- Necessary style for posts list -->
	<style type="text/css">
		.jp-latest-posts {
			list-style: none;
			overflow: hidden;
			display: block;
		}
		.jp-latest-posts li {
			border-bottom: 1px solid #E6E6E6;
			overflow: hidden;
			padding: 5px 0px;
			margin: 0px 3px;
		}
		.jp-latest-posts li:last-child {
			border-bottom: 0px;
		}
		.jp-latest-posts li img {
			float: left;
			margin: 4px 4px 4px 4px;
			padding: 2px;
			border: 1px solid #E6E6E6;
		}
		.jp-latest-posts li h5 {
			font-weight: normal;
			font-size: 14px;
			margin-top: 0px;
			line-height: 21px;
			text-align: left;
		}
		.jp-latest-posts li h5 a {
			color: #04A33C;
		}
		.jp-latest-posts li p {
			font-size: 12px;
			color: #9A9A9A;
		}
	</style>
	<!-- Creating Markup for latest posts -->
	<ul class="jp-latest-posts">
		<?php  
			$allcats = get_categories('child_of=0'); 
			foreach ($allcats as $cat) :
			$args = array(
				'posts_per_page' => $lp_settings['posts_count'], // set number of post per category here
				'category__in' => array($cat->term_id)
				);
			$customInCatQuery = new WP_Query($args);
				if ($customInCatQuery->have_posts()) :
				while ($customInCatQuery->have_posts()) : $customInCatQuery->the_post(); ?>
				<li>
					<?php 
						global $lp_options;
						$lp_settings = get_option( 'lp_options', $lp_options );
					?>
					<?php if( $lp_settings['featured_image'] ) : ?>
					<?php the_post_thumbnail( array( $lp_settings['feat_img_size'],$lp_settings['feat_img_size'] ) ); ?>
					<?php endif; ?>
					<h5><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h5>
					<?php if( $lp_settings['post_info'] ) : ?>
					<p>Posted by <?php the_author_posts_link(); ?> | <?php the_time('F jS, Y') ?> | <?php the_category(', '); ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?> <?php edit_post_link('| Edit'); ?></p>
					<?php endif; ?>
				</li>
		<?php endwhile; ?>
		<?php else: ?>
			<li>No post found</li>
		<?php endif; 
			wp_reset_query();
			endforeach; 
		?>
	</ul>
<?php }

// Creating shortcode for displaying to post or page

add_shortcode('latest-posts','jp_latest_posts_shortcode');



// Creating the widget 
class jlp_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'jlp_widget', 

// Widget name will appear in UI
__('JP Latest Posts Widget', 'jlp_widget_domain'), 

// Widget description
array( 'description' => __( 'A simple widget for listing latest post from your blog', 'jlp_widget_domain' ), ) 
);
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];

// This is where you run the code and display the output

jp_latest_posts_shortcode();
echo $args['after_widget'];
}
		
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'Latest Posts', 'jlp_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class jlp_widget ends here

// Register and load the widget
function jlp_load_widget() {
	register_widget( 'jlp_widget' );
}
add_action( 'widgets_init', 'jlp_load_widget' );

?>