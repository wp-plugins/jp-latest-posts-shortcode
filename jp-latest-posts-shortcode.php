<?php
/**
 * Plugin Name: JP Latest Posts Shortcode
 * Plugin URI: http://skjoy.info/plugins/jp-latest-posts-shortcode.html
 * Description: This is a simple plugin for listing latest posts from your blog.Use shortcode [latest-posts] to post or page.Then see,a list of latest posts will appear to your desired post or page.This plugin also contains a widget for showing latest post in widget/sidebar area.Enjoy...
 * Version: 1.0
 * Author: Skjoy
 * Author URI: http://skioy.info
 * Requires at least: 3.0
 * Tested Up to: 4.1.1
 * Stable Tag: 2.0
 * License: GPL v2
 * Shortname: lp or lp_
 */

include_once( 'lp-options.php' );

/* Adding Plugin custm CSS file */
wp_enqueue_style('jp-lps-css', plugin_dir_url(__FILE__).'css/style.css');
 
 // Creating main function for latest posts
 
function jp_latest_posts_shortcode() { ?>
	
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
					
					<?php
						if( $lp_settings['featured_image'] ) : 
					?>
					<?php 
						the_post_thumbnail( array( $lp_settings['feat_img_size'],
						$lp_settings['feat_img_size'] ) ); 
					?>
					<?php endif; ?>
					
					<h2>
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
					</h2>
					
					<?php if( $lp_settings['post_info'] ) : ?>
					<p>
						Posted by <?php the_author_posts_link(); ?> | <?php the_time('F jS, Y') ?> | <?php the_category(', '); ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?> <?php edit_post_link('| Edit'); ?>
					</p>
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