<?php
use WilokeListgoFunctionality\AlterTable\AlterTableFavirote;

class WilokeFavorite{
	public static $aTemporaryCaching;

	public function __construct() {
		add_action('wp_ajax_wiloke_listgo_fetch_favorites', array($this, 'fetchNewFavoriteItems'));
	}

	public static function totalMyFavorites(){
		if ( isset(self::$aTemporaryCaching['total_my_favorites']) ){
			return self::$aTemporaryCaching['total_my_favorites'];
		}

		global $wpdb;
		$tblName = $wpdb->prefix.AlterTableFavirote::$tblName;
		$totalFavorites = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(post_ID) FROM $tblName WHERE user_ID=%d",
				WilokePublic::$oUserInfo->ID
			)
		);

		self::$aTemporaryCaching['total_my_favorites'] = $totalFavorites;

		return self::$aTemporaryCaching['total_my_favorites'];
	}

	public static function renderFavoriteItem($post){
		$thumbnail = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID) : get_template_directory_uri() . '/img/no-image.jpg';
		?>
		<div class="f-listings-item">
			<div class="f-listings-item__media">
				<a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php Wiloke::lazyLoad($thumbnail); ?></a>
			</div>
			<div class="overflow-hidden">
				<h2 class="f-listings-item__title"><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo get_the_title($post->ID); ?></a></h2>
				<p><?php Wiloke::wiloke_content_limit(100, $post, false, $post->post_content, false); ?></p>
			</div>
			<div class="f-listings-item__meta">
                    <span>
                        <a class="js-remove-favorite" data-postid="<?php echo esc_attr($post->ID); ?>" href="#">
                            <i class="fa fa-trash-o"></i> <?php esc_html_e('Remove', 'listgo'); ?>
                        </a>
                    </span>
			</div>
		</div>
		<?php
	}

	public function fetchNewFavoriteItems(){
		if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error();
		}

		$postsPerPage = isset($_POST['postsperpage']) && absint($_POST['postsperpage']) <= 30 ?  $_POST['postsperpage'] : 10;
		$paged = isset($_POST['paged']) && !empty($_POST['paged']) ? absint($_POST['paged']) : 1;
		$offset = ($paged-1)*$postsPerPage;

		global $wpdb;
		$tblPosts = $wpdb->prefix . 'posts';
		$tblFavorite = $wpdb->prefix . AlterTableFavirote::$tblName;

		$aResults = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT $tblPosts.* FROM $tblPosts INNER JOIN (SELECT DISTINCT $tblFavorite.post_ID FROM $tblFavorite WHERE $tblFavorite.user_ID=%d ORDER BY $tblFavorite.post_ID DESC LIMIT $postsPerPage OFFSET $offset) as tblFavorites ON ($tblPosts.ID=tblFavorites.post_ID)",
				get_current_user_id()
			)
		);

		if ( !empty($aResults) && !is_wp_error($aResults) ){
			ob_start();
			foreach ( $aResults as $oResult ){
				self::renderFavoriteItem($oResult);
			}
			$content = ob_get_clean();
			wp_send_json_success(
				array(
					'total'     => count($aResults),
					'content'   => $content
				)
			);
		}else{
			wp_send_json_success(
				array(
					'total'=> 0,
					'content'   => esc_html__('There are no listings', 'listgo')
				)
			);
		}
	}
}