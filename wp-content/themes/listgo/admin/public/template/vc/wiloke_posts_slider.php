<?php
function wiloke_shortcode_posts_slider($atts){
	$atts = shortcode_atts(
		array(
			'post_type'      => '',
			'include'        => '',
			'order_by'       => '',
			'show_posts'     => '',
			'posts_per_page' => '',
			'image_size'     => '',
			'categories'     => '',
			'css'            => '',
			'extract_class'  => ''
		),
		$atts
	);

	$aArgs['post_status'] = 'publish';
	if ( $atts['post_type'] === 'include' ){
		$aArgs['post__in'] = explode(',', $atts['include']);
	}else{
		$aArgs['posts_per_page']    = $atts['posts_per_page'];
		$aArgs['post_type']         = $atts['post_type'];
		$aArgs['order_by']          = $atts['order_by'];

		if ( $aArgs['post_type'] == 'post' && !empty($atts['categories']) ){
			$aArgs['category__in'] = explode(',', $atts['categories']);
		}
	}

	$query = new WP_Query($aArgs);
	ob_start();
	if ( $query->have_posts() ) :
		$imgSize = strpos($atts['image_size'], 'x') !== false ? explode(',', $atts['image_size']) : $atts['image_size'];
		$wrapperClass = 'blog-carousel owl-carousel nav-bottom ' . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');

	?>
		<div class="<?php echo esc_attr($wrapperClass); ?>" data-showposts="<?php echo esc_attr($atts['show_posts']); ?>">
			<?php
            while ($query->have_posts()) : $query->the_post();
				$aDate = get_the_date("M/d", $query->post->ID);
				$aDate = explode('/', $aDate);
				$link = get_permalink($query->post->ID);
				$commentNumber = get_comments_number($query->post->ID);
				if ( $commentNumber > 2 ) {
					$commentTile = esc_html__('Comments', 'listgo');
				}else{
					$commentTile = esc_html__('Comment', 'listgo');
				}
			?>
			<div class="post post__grid">

				<?php
                if ( has_post_thumbnail($query->post->ID) ) :
                ?>
					<div class="post__media">
						<div class="images">
							<a href="<?php echo esc_url($link); ?>" class="owl-lazy bg-scroll" data-src="<?php echo esc_url(get_the_post_thumbnail_url($query->post->ID, $imgSize)); ?>"><?php echo get_the_post_thumbnail($query->post->ID, $imgSize); ?></a>
						</div>
						<span class="post__date">
							<span class="day"><?php echo esc_attr($aDate[1]); ?></span>
							<span class="month"><?php echo esc_attr($aDate[0]); ?></span>
						</span>
					</div>
				<?php endif; ?>

				<div class="post__body">

					<h2 class="post__title"><a href="<?php echo esc_url($link); ?>"><?php echo esc_html(get_the_title($query->post->ID)); ?></a></h2>

					<div class="post__meta">
						<?php if ( !has_post_thumbnail($query->post->ID) ) : ?>
							<span class="post__date">
								<span class="day"><?php echo esc_attr($aDate[1]); ?></span>
								<span class="month"><?php echo esc_attr($aDate[0]); ?></span>
							</span>
						<?php endif; ?>

						<?php if ( $atts['post_type'] === 'post' || $atts['post_type'] === 'listing' ) : ?>
                            <?php
							$tax = $atts['post_type'] === 'post' ? 'category' : 'listing_location';
							$aTerms = Wiloke::getPostTerms($query->post, $tax);
							if ( !empty($aTerms) && !is_wp_error($aTerms) ) :
							?>
							<span class="post__meta-cat">
                                <i class="icon_ribbon_alt"></i>
                                <a href="<?php echo esc_url(get_term_link($aTerms[0]->term_id)); ?>"><?php echo esc_html($aTerms[0]->name); ?></a>
                            </span>
						<?php endif; endif; ?>
						<span class="post__meta-comment">
                            <i class="icon_chat"></i>
                            <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($commentNumber . ' ' . $commentTile); ?></a>
                        </span>
					</div>

					<div class="post__entry">
						<p><?php Wiloke::wiloke_content_limit(100, null, false, $query->post->post_content, false); ?></p>
					</div>

					<div class="post__foot">
						<a href="<?php echo esc_url($link); ?>" class="post__more"><?php esc_html_e('Read More', 'listgo'); ?>
							<i class="fa fa-arrow-circle-o-right"></i>
						</a>
					</div>

				</div>
			</div>
			<?php endwhile; ?>
		</div>
	<?php
	endif;wp_reset_postdata();
	$content = ob_get_clean();
	return $content;
}