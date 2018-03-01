<?php
    global $post, $wiloke;

    $nextPost = get_next_post();
    $prevPost = get_previous_post();

    $parentUrl = '#';
    if ( $post->post_type == 'post' )
    {
        $parentUrl = $wiloke->aThemeOptions['single_post_navigation_back_to'];
    }else{
        $parentUrl = $wiloke->aThemeOptions['single_photo_navigation_back_to'];
    }
?>
<div class="single__nav">
    <?php
        if ( !empty($prevPost) ) :
            $thumb = '';
            if ( has_post_thumbnail($prevPost->ID) )
            {
                $thumb = wp_get_attachment_thumb_url(get_post_thumbnail_id($prevPost->ID), 'thumbnail');
            }
    ?>
    <a href="<?php echo esc_url(get_permalink($prevPost->ID)); ?>" class="single__nav-control single__nav-prev">

        <i class="pe-7s-play"></i>

        <span class="single__nav-title"><?php echo get_the_title($prevPost->ID); ?></span>

        <div class="single__nav-thumb bg-scroll" style="background-image: url(<?php echo esc_url($thumb); ?>)">
            <?php if ( !empty($thumb) ) : ?>
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($prevPost->post_title); ?>" />
            <?php endif; ?>
        </div>

    </a>
    <?php endif; ?>

    <a href="<?php echo esc_url($parentUrl); ?>" class="single__nav-link"><i class="pe-7s-keypad"></i></a>

    <?php
    if ( !empty($nextPost) ) :
    $thumb = '';
    if ( has_post_thumbnail($nextPost->ID) )
    {
        $thumb = wp_get_attachment_thumb_url(get_post_thumbnail_id($nextPost->ID), 'thumbnail');
    }
    ?>
    <a href="<?php echo esc_url(get_permalink($nextPost->ID)); ?>" class="single__nav-control single__nav-next">
        <div class="single__nav-thumb bg-scroll" style="background-image: url(<?php echo esc_url($thumb); ?>)">
            <?php if ( !empty($thumb) ) : ?>
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($nextPost->post_title); ?>" />
            <?php endif; ?>
        </div>

        <span class="single__nav-title"><?php echo get_the_title($nextPost->ID); ?></span>

        <i class="pe-7s-play"></i>
    </a>
    <?php endif; ?>

</div>