<div class="tab__content">
	<?php
    do_action('wiloke/listgo/templates/single-listing/after_open_content', $post);
	if ( defined('WILOKE_TUNROFF_DESIGN_ADDLISTING_UC') && WILOKE_TUNROFF_DESIGN_ADDLISTING_UC ) :
		if ( WilokePublic::toggleTabStatus('listing_toggle_tab_desc') === 'enable' ) :
			?>
            <div class="tab__panel active" id="tab-description">
				<?php do_action('wiloke/listgo/templates/single-listing/after_tab-description_open', $post); ?>
                <div class="listing-single__content">
					<?php
					do_action('wiloke/listgo/templates/single-listing/after_listing_content_open', $post);
					the_content();
					wp_link_pages();
					do_action('wiloke/listgo/templates/single-listing/before_listing_content_close', $post);
					?>
                </div>
				<?php do_action('wiloke/listgo/templates/single-listing/before_tab-description_close', $post); ?>
            </div>
			<?php
		endif;
		include get_template_directory(). '/templates/single-listing-elements/contact-tab.php';
		if ( WilokePublic::toggleTabStatus('listing_toggle_tab_review_and_rating') === 'enable' ) :
			?>
            <div class="tab__panel" id="tab-review">
				<?php comments_template(); ?>
            </div>
		<?php endif; ?>
    <?php
    else:
	    do_action('wiloke/listgo/template/single-listing-elements/tabs-content', $post);
    endif;
	do_action('wiloke/listgo/templates/single-listing/before_close_content', $post);
	?>
</div>