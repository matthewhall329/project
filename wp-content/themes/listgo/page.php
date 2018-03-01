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

$aPageSettings = get_post_meta($post->ID, 'page_settings', true);

if ( !empty($aPageSettings['header_image']) ){
    $featuredImg = $aPageSettings['header_image'];
    $overlayColor = $aPageSettings['header_overlay'];
}else{
    $featuredImg = get_the_post_thumbnail_url($post->ID, 'large');
    $overlayColor = empty($wiloke->aThemeOptions) && !empty($wiloke->aThemeOptions['blog_header_overlay']) ? $wiloke->aThemeOptions['blog_header_overlay']['rgba'] : '';
}

if ( empty($featuredImg) && !empty($wiloke->aThemeOptions) && !empty($wiloke->aThemeOptions['blog_header_image']) ){
    $featuredImg = wp_get_attachment_image_url($wiloke->aThemeOptions['blog_header_image']['id'], 'large');
    $featuredImg = $featuredImg ? $featuredImg : $wiloke->aThemeOptions['blog_header_image']['url'];
}

$wilokeSidebarLayout = $wiloke->aThemeOptions['page_sidebar'];
switch ($wilokeSidebarLayout){
	case 'left':
		$contentClass = 'col-md-9 col-md-push-3';
		break;
	case 'right':
		$contentClass = 'col-md-9';
		break;
	default:
		$contentClass = 'col-md-8 col-md-offset-2';
		break;
}

while ( have_posts() ) : the_post(); 
?>
    <div class="header-page bg-scroll lazy" data-src="<?php echo esc_url($featuredImg); ?>">
        <div class="container">
            <div class="header-page__inner">
                <h1 class="header-page__title"><?php echo esc_html($post->post_title); ?></h1>
            </div>
        </div>
        <div class="overlay" style="background-color: rgba(<?php echo esc_attr($overlayColor); ?>)"></div>
    </div>

    <div class="wo-blog">
        <div class="container">
            <div class="<?php echo esc_attr($contentClass); ?>">
                <div class="post post__single">
                    <div class="post__body">
                        <div class="post__entry">
                            <div class="single-feature-image">
                                <?php the_post_thumbnail(); ?>
                            </div>
                            <?php the_content(); ?>
                            <?php wp_link_pages(); ?>
                        </div>
                    </div>
                </div>
                <?php
                if ( !function_exists('is_woocommerce') || ( function_exists('is_woocommerce') && !is_woocommerce() && !is_account_page() && !is_cart() ) ) :
                    $nextPost = get_next_post();
                    $prevPost = get_previous_post();
                    if ( !empty($nextPost) || !empty($prevPost) ) : ?>
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
                        <?php
                    endif;
                endif;
                ?>
                <?php comments_template(); ?>
            </div>
	        <?php
	        if ( $wilokeSidebarLayout !== 'no' ){
		        get_sidebar();
	        }
	        ?>
        </div>
    </div>

	<?php

endwhile; 

wp_reset_postdata();

get_footer();