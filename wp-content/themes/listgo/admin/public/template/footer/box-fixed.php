<?php global $wiloke; ?>
<div class="box-fixed">

    <div class="box__top">

        <div class="table">
            <div class="table-cell">
                <?php if ( !empty($wiloke->aThemeOptions['general_slogan']) ) : ?>
                <h4 class="bm__name"><?php echo esc_html($wiloke->aThemeOptions['general_slogan']); ?></h4>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="box__right">

        <div class="table">
            <div class="table-cell">
                <div class="bm__scrollTop">
                    <span class="bm__scrollTop-text"><?php esc_html_e('SCROLL TO TOP', 'listgo'); ?></span>
                    <span class="bm__scrollTop-icon"><i class="pe-7s-download"></i></span>
                </div>
            </div>
        </div>

    </div>

    <div class="box__bottom">

        <div class="table">
            <div class="table-cell">
                <?php if ( !empty($wiloke->aThemeOptions['footer_copyright']) ) : ?>
                <div class="bm__copyright"><?php Wiloke::wiloke_kses_simple_html($wiloke->aThemeOptions['footer_copyright']); ?></div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="box__left">

        <div class="table">
            <div class="table-cell">
                <div class="bm__social">
                    <?php WilokeSocialNetworks::render_socials($wiloke->aThemeOptions); ?>
                </div>
            </div>
        </div>

    </div>

</div>