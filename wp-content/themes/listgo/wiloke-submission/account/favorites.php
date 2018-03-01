<?php
use WilokeListgoFunctionality\AlterTable\AlterTableFavirote;

?>
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <h2 class="author-page__title">
            <i class="icon_documents_alt"></i> <?php esc_html_e('My Favorites', 'listgo'); ?>
        </h2>
		<?php
		$postsPerPage = 10;
		global $wpdb;
		$tblPosts = $wpdb->prefix . 'posts';
		$tblFavorite = $wpdb->prefix . AlterTableFavirote::$tblName;

		$aResults = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT $tblPosts.* FROM $tblPosts INNER JOIN (SELECT DISTINCT $tblFavorite.post_ID FROM $tblFavorite WHERE $tblFavorite.user_ID=%d ORDER BY $tblFavorite.post_ID DESC LIMIT $postsPerPage) as tblFavorites ON ($tblPosts.ID=tblFavorites.post_ID)",
				WilokePublic::$oUserInfo->ID
			)
		);

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT($tblPosts.ID) FROM $tblPosts INNER JOIN (SELECT DISTINCT $tblFavorite.post_ID FROM $tblFavorite WHERE $tblFavorite.user_ID=%d ORDER BY $tblFavorite.post_ID) as tblFavorites ON ($tblPosts.ID=tblFavorites.post_ID)",
				WilokePublic::$oUserInfo->ID
			)
		);
		?>
        <div id="wiloke-listgo-my-favorites" class="account-page" data-total="<?php echo esc_attr($total); ?>" data-postsperpage="<?php echo esc_attr($postsPerPage); ?>">
			<?php if ( !empty($aResults) && !is_wp_error($aResults) ) : ?>
                <div id="wiloke-listgo-show-listings" class="f-listings">
					<?php
					foreach ($aResults as $oResult) :
						WilokeFavorite::renderFavoriteItem($oResult);
					endforeach;
					?>
                </div>
                <div style="margin-top: 40px;" id="wiloke-listgo-pagination" class="nav-links text-center"></div>
				<?php
			else:
				WilokeAlert::render_alert( __('There is no favorite yet', 'listgo'), 'info', false, false);
			endif; wp_reset_postdata();
			?>
        </div>
    </div>
</div>