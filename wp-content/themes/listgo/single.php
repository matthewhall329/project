<?php
/**
 * The template for displaying all single posts
 *
 * @link https://wiloke.com
 *
 * @package Wiloke/Themes
 * @subpackage Listgo
 * @since 1.0
 * @version 1.0
 */
get_header();
    global $wiloke;

    $featuredImg = get_the_post_thumbnail_url($post->ID, 'large');
    if ( empty($featuredImg) && !empty($wiloke->aThemeOptions) && !empty($wiloke->aThemeOptions['blog_header_image']) ){
        $featuredImg = wp_get_attachment_image_url($wiloke->aThemeOptions['blog_header_image']['id'], 'large');
        $featuredImg = $featuredImg ? $featuredImg : $wiloke->aThemeOptions['blog_header_image']['url'];
    }
    $aSettings = WilokePublic::getBlogSettings();
    $wilokeSidebarLayout = $aSettings['sidebar'];
    $overLayColor = empty($wiloke->aThemeOptions) && !empty($wiloke->aThemeOptions['blog_header_overlay']) ? $wiloke->aThemeOptions['blog_header_overlay']['rgba'] : '';
    while ( have_posts() ) : the_post();
?>
    <div class="header-page bg-scroll lazy" data-src="<?php echo esc_url($featuredImg); ?>">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class="header-page__single">
                        <div class="tb">
                            <div class="tb__cell">
                                <div class="header-page__post">
                                    <h1 class="post__title"><?php echo esc_html($post->post_title); ?></h1>
                                    <div class="post__meta">
                                        <span class="post__meta-author">
                                            <i class="fa fa-user"></i>
	                                        <a href="<?php echo esc_url(get_author_posts_url($post->post_author) .  "?&amp;target=blog"); ?>"><?php the_author(); ?></a>
                                        </span>

                                        <span class="post__meta-cat">
                                            <i class="icon_ribbon_alt"></i>
                                            <?php the_category(', '); ?>
                                        </span>

                                        <?php if ( get_comments_number() || comments_open()  ) : ?>
                                        <span class="post__meta-comment">
                                            <i class="icon_chat"></i>
                                            <span><?php WilokePublic::renderComment(); ?></span>
                                        </span>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay" style="background-color: rgba(<?php echo esc_attr($overLayColor); ?>)"></div>
    </div>

    <div class="wo-blog">
        <div class="container">
            <div class="row">
                <div class="<?php echo esc_attr($aSettings['main_class']); ?>">
                    <div class="post post__single">
                        <div class="post__body">
                            <div class="post__entry">
                                <?php the_content(); ?>
                                <?php wp_link_pages(); ?>
                            </div>
	                        <?php the_tags( '<div class="post__foot"><div class="post__tags"><span>'.esc_html__('Tags', 'listgo').'</span> ', ', ', '</div></div>' ); ?>
                        </div>
                    </div>

                    <?php
                    $nextPost = get_next_post();
                    $prevPost = get_previous_post();
                    if ( !empty($nextPost) || !empty($prevPost) ) :
                    ?>
                    <div class="pagination_post">
                        <div class="row">
                            <div class="col-xs-6">
                                <a href="<?php echo esc_url(get_permalink($prevPost)); ?>" class="pagination_post__prev <?php echo empty($prevPost) ? 'disabled' : '';?>"><?php esc_html_e('Previous article', 'listgo'); ?></a>
                            </div>
                            <div class="col-xs-6">
                                <a href="<?php echo esc_url(get_permalink($nextPost)); ?>" class="pagination_post__next <?php echo empty($nextPost) ? 'disabled' : '';?>"><?php esc_html_e('Next article', 'listgo'); ?></a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php comments_template(); ?>

	                <?php do_action('wiloke/listgo/single/before_related_post', $post); ?>
                    <!-- Related Posts -->
	                <?php WilokePublic::renderRelatedPosts(); ?>
                    <!-- End / Related Posts -->

                </div>
                <?php
                if ( $aSettings['sidebar'] !== 'no' ){
                    get_sidebar();
                }
                ?>
            </div>
        </div>
    </div>
<?php
    endwhile; wp_reset_postdata();
get_footer();