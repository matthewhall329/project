<?php
function wiloke_shortcode_testimonials($atts){
	$atts = shortcode_atts(
		array(
			'bg_image' => '',
			'css'      => '',
			'extract_class'  => ''
		),
		$atts
	);

	$query = new WP_Query(
		array(
			'post_type'         => 'testimonial',
			'posts_per_page'    => 50,
			'post_status'       => 'publish'
		)
	);

	$wrapperClass = $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');

	$wrapperClass = trim($wrapperClass);

	ob_start();

	if ( $query->have_posts() ) : ?>

		<div class="<?php echo esc_attr($wrapperClass); ?>">

			<div class="testimonials">

				<div class="testimonial__avatars owl-carousel">

					<?php
						$aContents = array();
						while ( $query->have_posts() ) :
							$query->the_post();
							$aSettings = Wiloke::getPostMetaCaching($query->post->ID, 'testimonial_settings');
							$id = 'ttn'.$query->post->ID;
							$aContents[$id]['content']  = get_the_content($query->post->ID);
							$aContents[$id]['name']     = get_the_title($query->post->ID);
							$aContents[$id]['position'] = $aSettings['position'];
							if ( !empty($aSettings['profile_picture']) ) : ?>
								<a>
                                    <img class="lazy" src="<?php echo esc_url($aSettings['profile_picture']); ?>" alt="<?php echo esc_attr($query->post->post_title); ?>">
                                </a>
							<?php endif;
						endwhile; 
					?>

				</div>

				<div class="testimonials-carousel nav-middle owl-carousel">

					<?php foreach($aContents as $id => $aContent) : ?>

					<div class="testimonials__panel">

						<div class="testimonial__content">
							<?php Wiloke::wiloke_kses_simple_html($aContent['content']); ?>
						</div>

						<h6 class="testimonial__name"><?php echo esc_html($aContent['name']); ?></h6>

						<span class="testimonial__pos"><?php echo esc_html($aContent['position']); ?></span>

					</div>

					<?php endforeach; ?>

				</div>

			</div>
		
		</div>

		<?php

	endif; wp_reset_postdata();

	$content = ob_get_clean();

	return $content;
}