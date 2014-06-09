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
 
function jp_latest_posts_shortcode() { ?>
	<style type="text/css">
		.jp-latest-posts {
			list-style: none;
			display: block;
		}
		.jp-latest-posts li {
			border-bottom: 1px solid #E6E6E6;
			overflow: hidden;
			padding: 5px 0px;
			margin: 0px 3px;
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

add_shortcode('latest-posts','jp_latest_posts_shortcode');

?>