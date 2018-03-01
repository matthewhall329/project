<!-- Footer -->
<footer id="footer">

    <div class="footer__container bg-scroll" style="background-image: url(<?php echo esc_url($wiloke->aThemeOptions['footer_background']['url']); ?>);">

        <div class="logo-footer">
            <?php if ( !empty($wiloke->aThemeOptions['footer_logo']['url']) ) : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url($wiloke->aThemeOptions['footer_logo']['url']); ?>" alt="<?php echo esc_attr(get_option('blogname')); ?>" /></a>
            <?php endif; ?>
        </div>

        <?php
            $key = $wiloke->aConfigs['frontend']['register_nav_menu']['menu'][1]['key'];
            
            if ( has_nav_menu($key) )
            {
                wp_nav_menu($wiloke->aConfigs['frontend']['register_nav_menu']['config'][$key]);
            }

            if ( !empty($wiloke->aThemeOptions['footer_overlay']['rgba']) )
            {
                $overlayColor = $wiloke->aThemeOptions['footer_overlay']['rgba'];
            }elseif ( !empty($wiloke->aThemeOptions['footer_overlay']['color']) )
            {
                $overlayColor = $wiloke->aThemeOptions['footer_overlay']['color'];
            }else{
                $overlayColor = 'rgba(0,0,0,0.4)';
            }
        ?>

        <div class="overlay" style="background-color: <?php echo esc_attr($overlayColor); ?>;"></div>

    </div>

</footer>
<!-- End / Footer -->