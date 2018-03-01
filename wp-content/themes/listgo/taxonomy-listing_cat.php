<?php
get_header();
WilokePublic::headerPage();
global $wiloke, $wilokeSidebarPosition;

$oQuery = get_queried_object();
$layout = isset($wiloke->aThemeOptions['listing_taxonomy_layout']) ? $wiloke->aThemeOptions['listing_taxonomy_layout'] : 'listing--list';
$imgSize = strpos($layout, 'list') === -1 ? 'wiloke_listgo_370x370' : 'wiloke_listgo_455x340';
switch ( $wiloke->aThemeOptions['listing_location_category_sidebar'] ){
	case 'left':
		$mainClass = 'col-md-9 col-md-push-3';
		$wilokeSidebarPosition = 'left';
		break;
	case 'right':
		$mainClass = 'col-md-9';
		$wilokeSidebarPosition = 'right';
		break;
	default:
		$mainClass = 'col-md-12';
		$wilokeSidebarPosition = 'no';
		break;
}
?>
	<div class="page-content">
		<div class="container">
            <div class="row">
                <div class="<?php echo esc_attr($mainClass); ?>">
                    <div class="listgo-listlayout-on-page-template">
                        <div class="from-wide-listing">
                        	<div class="from-wide-listing__header">
                        		<span class="from-wide-listing__header-title"><?php echo esc_html__('Filter', 'listgo') ?></span>
                        		<span class="from-wide-listing__header-close"><span>×</span> <?php echo esc_html__('Close', 'listgo') ?></span>
                        	</div>
                            <?php WilokeSearchSystem::searchForm(null, true); ?>
                            <div id="listgo-mobile-search-only" class="from-wide-listing__footer">
                            	<span><?php echo esc_html__('Apply', 'listgo') ?></span>
                            </div>
                        </div>
                        <?php do_action('wiloke/listgo/taxonomy-listing_cat/before_content'); ?>
                        <?php echo do_shortcode('[wiloke_listing_layout display_style="pagination" order_by="post_date" show_terms="listing_location" get_posts_from="listing_cat" listing_cat="'.$oQuery->term_id.'" image_size="'.$imgSize.'" layout="'.$layout.'" posts_per_page="'.get_option('posts_per_page').'" filter_type="none"]'); ?>
	                    <?php do_action('wiloke/listgo/taxonomy-listing_cat/after_content'); ?>
                    </div>
                </div>
	            <?php
	            if ( $wilokeSidebarPosition !== 'no' ) {
		            get_sidebar('taxlisting');
	            }
	            ?>
            </div>
		</div>
	</div>
<?php
get_footer();