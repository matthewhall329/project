<?php
    if ( $atts['layout'] == 'creative' ) {
        $class = '';
        $thumbnailSize = 'large';
    }else{
        if ( $order <= 2 && ($atts['layout'] == 'style2') ) {
            $class = 'col-xs-12 col-sm-6 col-md-6';
        }else{
            $class = 'col-xs-12 col-sm-6 col-md-4';
            if ( $order == 5 ) {
                $order = 0;
            }
        }
        $thumbnailSize = 'medium';
    }
    $headerImage = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID, $thumbnailSize) : '';

    $link = get_permalink($post->ID);
    $title = get_the_title();
?>
<?php if ( $atts['layout'] != 'creative' ) : ?>
<div class="<?php echo esc_attr($class); ?>">
<?php endif; ?>
    <div class="post">
        <?php if ( !empty($headerImage) ) : ?>
        <div style="background-image: url(<?php echo esc_url($headerImage); ?>)" class="post__media">
            <a href="<?php echo esc_url($link) ?>">
                <img src="<?php echo esc_url($headerImage); ?>" alt="<?php echo esc_attr($title); ?>" />
            </a>
        </div>
        <?php endif; ?>

        <div class="post__body">
            <h2 class="post__title"><a href="<?php echo esc_url($link); ?>"><?php Wiloke::wiloke_kses_simple_html($title); ?></a></h2>
            <?php WilokePublic::render_post_meta(); ?>
        </div>
    </div>
<?php if ( $atts['layout'] != 'creative' ) : ?>
</div>
<?php endif; ?>