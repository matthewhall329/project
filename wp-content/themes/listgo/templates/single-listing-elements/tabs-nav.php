<ul class="tab__nav">
    <?php
        do_action('wiloke/listgo/templates/single-listing/top_nav_tab', $post);

        if ( defined('WILOKE_TUNROFF_DESIGN_ADDLISTING_UC') && WILOKE_TUNROFF_DESIGN_ADDLISTING_UC ){
	        WilokePublic::renderListingTab('description');
	        WilokePublic::renderListingTab('contact');
	        WilokePublic::renderListingTab('review');
        }else{
	        do_action('wiloke/listgo/template/single-listing-elements/tabs-nav', $post);
        }

        do_action('wiloke/listgo/templates/single-listing/bottom_nav_tab', $post);
    ?>
</ul>