<?php
get_header();
global $wiloke, $wilokeSidebarLayout;
WilokePublic::headerPage();
?>
<div class="wo-blog">
	<div class="container">
		<div class="row">
			<?php
			$aSettings = WilokePublic::getBlogSettings();
			$wilokeSidebarLayout = $aSettings['sidebar'];

			if ( is_page_template('templates/blog-standard.php') || is_page_template('templates/blog-grid.php') ){
			    query_posts(
                    array(
                        'post_type'     => 'post',
                        'post_status'   => 'publish',
                        'posts_per_page'=> get_option('posts_per_page'),
                        'paged'         => get_query_var('paged', 1)
                    )
                );
            }
			?>
			<?php if ( have_posts() ) : ?>
			<div class="<?php echo esc_attr($aSettings['main_class']); ?>">
				<div class="wil-blog">
					<div class="row row-clear-lines">
						<?php while (have_posts()) : the_post();  ?>
							<div class="<?php echo esc_attr($aSettings['item_class']); ?>">
								<div <?php post_class("post ".$aSettings['layout']) ?>>

									<?php if ( has_post_thumbnail($post->ID) ) : ?>
									<div class="post__media">
										<div class="images">

											<?php if ( $aSettings['layout'] =='post__grid'): ?>
											<a class="bg-scroll lazy" data-src="<?php echo esc_url(get_the_post_thumbnail_url($post->ID, $aSettings['img_size'])); ?>" href="<?php echo esc_url(get_permalink($post->ID)); ?>">
											<?php else : ?>
											<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
											<?php endif ?>
												<?php echo get_the_post_thumbnail($post->ID, $aSettings['img_size']); ?>
											</a>
										</div>
										<?php WilokePublic::renderPostDateOnBlog(); ?>
									</div>
									<?php endif; ?>

									<div class="post__body">

										<h2 class="post__title"><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo esc_html($post->post_title); ?></a></h2>

										<div class="post__meta">
<span class="post__meta-author">
                                            <i class="fa fa-user"></i>
	                                        <a href="<?php echo esc_url(get_author_posts_url($post->post_author) .  "?&amp;target=blog"); ?>"><?php the_author(); ?></a>
                                        </span>
											<?php 
											if ( !has_post_thumbnail($post->ID) ){
												WilokePublic::renderPostDateOnBlog();
											}
											?>

	                                        <span class="post__meta-cat">
	                                            <i class="icon_ribbon_alt"></i>
	                                            <?php the_category(', '); ?>
	                                        </span>
											<span class="post__meta-comment">
	                                            <i class="icon_chat"></i>
	                                            <a href="<?php echo esc_url(get_permalink($post->ID).'#comments'); ?>"><?php WilokePublic::renderComment(); ?></a>
	                                        </span>
										</div>

										<div class="post__entry">
											<p><?php Wiloke::wiloke_content_limit($aSettings['limit_character'], $post, false, $post->post_content, false); ?></p>
										</div>

										<div class="post__foot">
											<a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="post__more"><?php esc_html_e('Read More', 'listgo'); ?>
												<i class="fa fa-arrow-circle-o-right"></i>
											</a>
										</div>

									</div>

								</div>
							</div>
						<?php endwhile; ?>
					</div>
				</div>
				<?php WilokePublic::renderPagination(); ?>
			</div>
			<?php
			if ( $aSettings['sidebar'] !== 'no' ){
				get_sidebar();
			}
			?>
			<?php else: ?>
                <?php get_template_part('content', 'none'); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
get_footer();
