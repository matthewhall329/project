<?php
use WilokeListGoFunctionality\Submit\User as WilokeSubmissionUser;
use WilokeListgoFunctionality\AlterTable\AlterTableReviews;

class WilokeReview{
	public static $scoreThanksForReviewingKey = 'wiloke_listgo_score_thanks_reviewing';
	public static $thanksForReviewingKey = 'wiloke_listgo_thanks_reviewing';

	public function __construct() {
		add_action('wp_ajax_nopriv_wiloke_listgo_fetch_new_reviews', array($this, 'fetchNewReviews'));
		add_action('wp_ajax_wiloke_listgo_fetch_new_reviews', array($this, 'fetchNewReviews'));

		add_action('wp_ajax_nopriv_wiloke_listgo_submit_review', array($this, 'submitReview'));
		add_action('wp_ajax_wiloke_listgo_submit_review', array($this, 'submitReview'));

		add_action('wp_ajax_nopriv_wiloke_listgo_thanks_reviewing', array($this, 'thanksForReviewing'));
		add_action('wp_ajax_wiloke_listgo_thanks_reviewing', array($this, 'thanksForReviewing'));
	}

	public static function isReviewed($userID, $postID) {
		global $wpdb;
		$tblRating  = $wpdb->prefix . AlterTableReviews::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT user_ID FROM $tblRating WHERE user_ID=%d and post_ID=%d",
				$userID, $postID
			)
		);
	}

	public static function getTopRatedListings($limit, $offset=0){
		global $wpdb;
		if ( !class_exists('WilokeListGoFunctionality\AlterTable\AlterTableReviews') ){
			return false;
		}
		$tblPosts   = $wpdb->prefix . 'posts';
		$tblRating  = $wpdb->prefix . AlterTableReviews::$tblName;

		$sql = "SELECT $tblPosts.ID, $tblPosts.post_title as title, $tblPosts.post_date, AVG($tblRating.rating) as rated_score, $tblRating.user_ID FROM $tblPosts LEFT JOIN $tblRating ON ($tblRating.post_ID = $tblPosts.ID) WHERE $tblPosts.post_type=%s AND $tblPosts.post_status=%s GROUP BY $tblPosts.ID ORDER BY rated_score DESC LIMIT $limit OFFSET $offset";

		$aResult = $wpdb->get_results(
			$wpdb->prepare(
				$sql,
				'listing', 'publish'
			),
			ARRAY_A
		);

		return $aResult;
	}

	public static function calculateRating(){
		global $wpdb, $post;
		$tblRating = $wpdb->prefix . \WilokeListgoFunctionality\AlterTable\AlterTableReviews::$tblName;
		$oResult = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT COUNT($tblRating.rating) as number_of_ratings, SUM(rating) as total, SUM(IF(rating=5, rating, 0)) as five_stars, SUM(IF(rating=4, rating, 0)) as four_stars, SUM(IF(rating=3, rating, 0)) as three_stars, SUM(IF(rating=2, rating, 0)) as two_stars, SUM(IF(rating=1, rating, 0)) as one_star FROM $tblRating WHERE post_ID=%d",
				$post->ID
			),
			ARRAY_A
		);

		return $oResult;
	}

	public static function averageRating($aAverages){
		$prefix = $aAverages['number_of_ratings'] > 1 ? esc_html__('Ratings', 'listgo') : esc_html__('Rating', 'listgo');
		$badStars = round($aAverages['total']/$aAverages['number_of_ratings'], 1);
		?>
        <li class="review-rating__label">
			<?php self::renderStars($badStars); ?>
            <span class="review-rating__label-title"><?php echo esc_html($aAverages['number_of_ratings']) . ' ' . $prefix; ?></span>
        </li>
		<?php
	}

	public static function diagramLineStars($aAverages, $badStars=0){
		switch ($badStars ){
			case 5:
				$key = 'five_stars';
				break;
			case 4:
				$key = 'four_stars';
				break;
			case 3:
				$key = 'three_stars';
				break;
			case 2:
				$key = 'two_stars';
				break;
			default:
				$key = 'one_star';
				break;
		}

		$average = round($aAverages[$key]/$aAverages['total']*100);
		?>
        <li class="review-rating__item">
			<?php self::renderStars($badStars); ?>
            <div class="review-rating__bar">
                <div class="review-rating__bar-percent" style="width: <?php echo esc_attr($average) ?>%"></div>
            </div>
        </li>
		<?php
	}

	public static function getStarClass($score, $compareWith){
		if ( $compareWith < $score ){
			$class = 'fa fa-star';
		}elseif ($compareWith == $score){
			$class = 'fa fa-star';
		}else{
			$class = ceil($score) == $compareWith ? 'fa fa-star-half-o' : 'fa fa-star-o';
		}
		return $class;
	}

	public static function renderStars($score){
		?>
        <span class="review-rating__star">
        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
            <i class="<?php echo esc_attr(self::getStarClass($score, $i)); ?>"></i>
        <?php endfor; ?>
        </span>
		<?php
	}

	public static function renderReviewItem($oResult){
		$aUserInfo = WilokePublic::getUserMeta($oResult->user_ID);
		$aThanksReviewing = Wiloke::getPostMetaCaching($oResult->ID, self::$thanksForReviewingKey);
		$actived = '';
		$countThanks = 0;
		if ( $aThanksReviewing ){
			$currentUser = !empty(WilokePublic::$oUserInfo) ? WilokePublic::$oUserInfo->ID : Wiloke::clientIP();
			$actived = in_array($currentUser, $aThanksReviewing) ? 'active disabled' : '';
			$countThanks = count($aThanksReviewing);
		}
		$avatar = Wiloke::getUserAvatar($oResult->user_ID, $aUserInfo, array(90, 90));
		?>
		<li class="comment" data-reviewid="<?php echo esc_attr($oResult->ID); ?>">
			<div class="comment__inner">
				<div class="comment__avatar">
					<a href="<?php echo get_author_posts_url($oResult->user_ID); ?>">
						<?php
						if ( strpos($avatar, 'profile-picture.jpg') === false ) {
							?>
							<img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($aUserInfo['display_name']); ?>" height="150" width="150" class="avatar">
							<?php
						} else {
							$firstCharacter = strtoupper(substr($aUserInfo['display_name'], 0, 1));
							echo '<span style="background-color: '.esc_attr(WilokePublic::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
						}
						?>
					</a>
				</div>

				<div class="comment__body">

                    <span class="listgo__rating">
                        <span class="rating__star">
                            <?php
                            for ( $i = 1; $i<=5; $i++ ) :
	                            $startStatus = $i <= absint($oResult->rating) ? 'fa fa-star' : 'fa fa-star-o';
	                            ?>
	                            <i class="<?php echo esc_attr($startStatus); ?>"></i>
                            <?php endfor; ?>
                        </span>
                        <span class="rating__number"><?php echo esc_html($oResult->rating); ?></span>
                    </span>

					<div class="comment__by-role">
						<span class="comment__by"><!-- <span><?php echo esc_html__('By ', 'listgo') ?></span> --><?php echo esc_html($aUserInfo['display_name']); ?></span>

						<?php WilokePublic::renderBadge($aUserInfo['role']); ?>
						<span class="comment__date"><?php echo esc_html(date('M d, Y', strtotime($oResult->post_date))); ?></span>
					</div>

					<h3 class="comment__name"><?php echo esc_html($oResult->post_title); ?></h3>

					<div class="comment__content">
						<?php Wiloke::wiloke_kses_simple_html(wpautop($oResult->post_content)); ?>
						<?php
						$aReviewSettings = Wiloke::getPostMetaCaching($oResult->ID, 'review_settings');
						if ( isset($aReviewSettings['gallery']) && !empty($aReviewSettings['gallery']) ) :
							echo '<div class="comment__gallery popup-gallery">';
							foreach ( $aReviewSettings['gallery'] as $galleryID => $originalImg ) :
								if ( empty($galleryID) || !is_numeric($galleryID) ){
									continue;
								}
								$thumb = wp_get_attachment_image_url($galleryID, 'thumbnail');
								?>
								<a href="<?php echo esc_url($originalImg); ?>" class="bg-scroll lazy" data-src="<?php echo esc_url($thumb); ?>"><img class="lazy" data-src="<?php echo esc_url($originalImg); ?>" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="<?php echo esc_attr($oResult->post_title); ?>"></a>
								<?php
							endforeach;
							echo '</div>';
						endif;
						?>
					</div>

					<div class="comment__reaction">
						<!-- <ul class="comment__reaction-list">
							<li>
								<a href="#">
									<i class="wil-icon wil-icon-like"></i>
									<span>Like</span>
								</a>
							</li>
							<li>
								<a href="#">
									<i class="wil-icon wil-icon-love"></i>
									<span>Love</span>
								</a>
							</li>
							<li>
								<a href="#">
									<i class="wil-icon wil-icon-haha"></i>
									<span>Haha</span>
								</a>
							</li>
							<li>
								<a href="#">
									<i class="wil-icon wil-icon-wow"></i>
									<span>Wow</span>
								</a>
							</li>
							<li>
								<a href="#">
									<i class="wil-icon wil-icon-sad"></i>
									<span>Sad</span>
								</a>
							</li>
							<li>
								<a href="#">
									<i class="wil-icon wil-icon-angry"></i>
									<span>Angry</span>
								</a>
							</li>
						</ul> -->
						<!-- <a class="wiloke-listgo-thanks-for-reviewing comment-like <?php echo esc_attr($actived); ?>" data-id="<?php echo esc_attr($oResult->ID); ?>" href="#" data-reaction="like">
                            <i class="wil-icon wil-icon-like"></i>
                        </a> -->

						<a class="wiloke-listgo-thanks-for-reviewing comment-like <?php echo esc_attr($actived); ?>" data-id="<?php echo esc_attr($oResult->ID); ?>" href="#">
							<i class="wil-icon wil-icon-like"></i>
							<span class="comment-like__count"><?php echo esc_html($countThanks); ?></span>
						</a>
						<!-- <div class="wil-reacted">
							<div class="wil-reacted__item">
								<i class="wil-icon wil-icon-like"></i>
								<span>22</span>
							</div>
							<div class="wil-reacted__item">
								<i class="wil-icon wil-icon-love"></i>
								<span>3</span>
							</div>
							<div class="wil-reacted__item">
								<i class="wil-icon wil-icon-haha"></i>
								<span>14</span>
							</div>
						</div> -->
					</div>
				</div>
			</div>
		</li>
		<?php
	}

	public function thanksForReviewing(){
		if ( !isset($_POST['post_ID']) || empty($_POST['post_ID']) || !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error();
		}

		$postType = get_post_field('post_type', $_POST['post_ID']);
		if ( $postType !== 'review' ){
			wp_send_json_error();
		}
		$userID = get_current_user_id();
		$userID = !empty($userID) ? $userID : Wiloke::clientIP();
		$aCurrent = Wiloke::getPostMetaCaching($_POST['ID'], self::$thanksForReviewingKey);
		$aCurrent = !empty($aCurrent) ? $aCurrent : array();
		$aCurrent[] = $userID;

		$score = Wiloke::getPostMetaCaching($_POST['ID'], self::$scoreThanksForReviewingKey);
		$score = empty($score) ? 1 : $score + 1;

		update_post_meta($_POST['post_ID'], self::$scoreThanksForReviewingKey, $score);
		update_post_meta($_POST['post_ID'], self::$thanksForReviewingKey, $aCurrent);
		Wiloke::setPostMetaCaching($_POST['post_ID'], self::$thanksForReviewingKey, $aCurrent);
		wp_send_json_success();
	}

	public function submitReview(){
		$canUploadFile = current_user_can('upload_files');
		$aData = $_POST;

		if ( !isset($aData['post_ID']) || empty($aData['post_ID']) || !isset($aData['wiloke-listgo-nonce-field']) || empty($aData['wiloke-listgo-nonce-field']) ){
			wp_send_json_error();
		}

		global $wiloke;
		if ( !isset($aData['review']) || empty($aData['review']) ){
			wp_send_json_error(
				array(
					'review' => esc_html__('Please write something about your review', 'listgo')
				)
			);
		}

		if ( !isset($aData['title']) || empty($aData['title']) ){
			wp_send_json_error(
				array(
					'review' => esc_html__('Please enter a title for your review', 'listgo')
				)
			);
		}

		$postType = get_post_field('post_type', $aData['post_ID']);

		if ( $postType !== 'listing' ){
			wp_send_json_error();
		}

		$rating = !isset($aData['rating']) || empty($aData['rating']) || ( absint($aData['rating']) > 5 ) ? 5: absint($aData['rating']);
		$userID = get_current_user_id();

		if ( empty($userID) ){
			if ( empty($aData['username']) ){
				wp_send_json_error(array(
					'username' => esc_html__('We need your username', 'listgo')
				));
			}

			if ( empty($aData['email']) ){
				wp_send_json_error(array(
					'email' => $wiloke->aConfigs['translation']['wrongemail']
				));
			}

			if ( empty($aData['password']) ){
				wp_send_json_error(array(
					'password' => esc_html__('We need your password', 'listgo')
				));
			}

			$aVerifyEmail = WilokeSubmissionUser::verifyEmail($aData['email']);
			if ( !$aVerifyEmail['success'] ){
				wp_send_json_error(array(
					'email' => $aVerifyEmail['message']
				));
			}

			$aVerifyUsername = WilokeSubmissionUser::verifyUserName($aData['username']);
			if ( !$aVerifyUsername['success'] ){
				wp_send_json_error(array(
					'username' => $aVerifyUsername['message']
				));
			}

			if ( isset($wiloke->aThemeOptions['toggle_google_recaptcha']) && ($wiloke->aThemeOptions['toggle_google_recaptcha'] == 'enable') ){
				$aVerifiedreCaptcha = WilokeSubmissionUser::verifyGooglereCaptcha($aData['g-recaptcha-response']);
				if ( $aVerifiedreCaptcha['status'] == 'error' ){
					wp_send_json_error(array(
						'g-recaptcha' => $aVerifiedreCaptcha['message']
					));
				}
				unset($aData['g-recaptcha-response']);
			}

			$aResult = WilokeSubmissionUser::createUserByEmail($aData['email'], $aData['password'], $aData['username']);
			if ( $aResult['success'] === false ){
				wp_send_json_error(array(
					'email' => $aResult['message']
				));
			}
			$isRefresh = true;
			$userID = $aResult['message'];
		}

		if ( self::isReviewed($userID, $aData['post_ID']) ) {
			wp_send_json_error(
				array(
					'alert' => esc_html__('Oops! It seems you have already left a review for this article.', 'listgo')
				)
			);
		}

		if ( isset($wiloke->aThemeOptions['toggle_approved_review_immediately']) && $wiloke->aThemeOptions['toggle_approved_review_immediately'] == 'disable' ) {
            $postStatus = 'pending';
		}else {
			$postStatus = 'publish';
		}

		$postID = wp_insert_post(
			array(
				'post_title'   => $aData['title'],
				'post_content' => $aData['review'],
				'post_status'  => $postStatus,
				'post_type'    => 'review'
			)
		);

		// If, in case, customer upload photos
		if ( $canUploadFile ){
			$aAttachIDs = explode(',', $aData['upload_photos']);
		}else{
			if ( isset($_FILES['upload_photos']) && !empty($_FILES['upload_photos']) ){
				$aAttachIDs = array();
				$aPhotos = $_FILES['upload_photos'];
				$aConditionals = array('image/jpeg', 'image/png', 'image/gif', 'image/jpg');
				$size = WilokePublic::getMaxFileSize();
				$size = absint(str_replace('M', '',$size));

				$allowKB = 1000*1000*$size;

				foreach ( $aPhotos['name'] as $key => $name ){

					if ( empty($aPhotos['error'][$key]) && ($aPhotos['size'][$key] <= $allowKB) ){
						if ( in_array($aPhotos['type'][$key], $aConditionals) ){
							$aFileUpload = array(
								'name'     => $aPhotos['name'][$key],
								'type'     => $aPhotos['type'][$key],
								'tmp_name' => $aPhotos['tmp_name'][$key],
								'error'    => $aPhotos['error'][$key],
								'size'     => $aPhotos['size'][$key]
							);
							$aAttachIDs[] = WilokePublic::insertAttachment($aFileUpload);
						}
					}
				}
			}
		}

		if ( !empty($aAttachIDs) ){
			$aGallerySettings = Wiloke::getPostMetaCaching($postID, 'rewiew_settings');
			$aGalleryData = array();
			foreach ( $aAttachIDs as $galleryID ){
				$aGalleryData[$galleryID] = wp_get_attachment_image_url($galleryID, 'full');
			}
			$aGallerySettings['gallery'] = $aGalleryData;
			update_post_meta($postID, 'review_settings', $aGallerySettings);
		}

		global $wpdb;
		$tblName = $wpdb->prefix . \WilokeListgoFunctionality\AlterTable\AlterTableReviews::$tblName;
		$wpdb->insert(
			$tblName,
			array(
				'user_ID'   => isset($userID) ? $userID : $userID,
				'post_ID'   => absint($aData['post_ID']),
				'review_ID' => $postID,
				'rating'    => $rating
			),
			array(
				'%d',
				'%d',
				'%d',
				'%d'
			)
		);
		update_post_meta($aData['post_ID'], self::$scoreThanksForReviewingKey, 0);

		$commentsCount = get_comments_number($aData['post_ID']);
		$tblPosts = $wpdb->prefix . 'posts';
		$wpdb->update(
			$tblPosts,
			array(
				'comment_count' => absint($commentsCount)+1
			),
			array(
				'ID' => absint($aData['post_ID'])
			),
			array(
				'%d'
			),
			array(
				'%d'
			)
		);

		do_action('wiloke/wiloke_submission/save_review', $postID, $aData['post_ID'], $userID);
		if ( isset($isRefresh) ){
			wp_send_json_success(
				array(
					'message' => esc_html__('Thanks for your reviewing!', 'listgo'),
					'refresh' => 'yes',
					'review_ID' => $postID
				)
			);
		}else{
			if ( isset($wiloke->aThemeOptions['toggle_approved_review_immediately']) && $wiloke->aThemeOptions['toggle_approved_review_immediately'] == 'disable' ) {
				wp_send_json_success(
					array(
						'review'  =>  '<li class="comment" data-reviewid="'.esc_attr($postID).'"><div class="comment__inner" style="font-style: italic">'.esc_html__('Your comment has been received and is being reviewed by our team staff. It will be published right after passing this step.', 'listgo') . '</div></li>',
						'refresh' => 'no',
						'review_ID' => $postID
					)
				);
			}else {
				$oResult = self::fetchReview('post_date', $postID, $aData['post_ID']);
				ob_start();
				self::renderReviewItem($oResult[0]);
				$review = ob_get_clean();

				wp_send_json_success(
					array(
						'message' => esc_html__('Thanks for your reviewing!', 'listgo'),
						'review'  => $review,
						'refresh' => 'no',
						'review_ID' => $postID
					)
				);
			}
		}
	}

	public static function fetchReview($orderBy = 'post_date', $reviewID=0, $postID=null, $offset=0){
		global $wpdb, $post;
		$limit = get_option('comments_per_page');
		$tblPosts   = $wpdb->prefix . 'posts';
		$tblRating  = $wpdb->prefix . \WilokeListgoFunctionality\AlterTable\AlterTableReviews::$tblName;

		if ( !empty($offset) ){
			$offset = ($offset-1)*$limit;
		}

		$postID = !empty($postID) ? $postID : $post->ID;

		if ( $orderBy === 'post_date' ){
			$sql = "SELECT $tblPosts.ID, $tblPosts.post_title, $tblPosts.post_date, $tblPosts.post_content, $tblRating.rating, $tblRating.user_ID FROM $tblPosts LEFT JOIN $tblRating ON ($tblRating.review_ID = $tblPosts.ID) WHERE $tblRating.post_ID=%d AND $tblPosts.post_type=%s AND $tblPosts.post_status=%s";

			if ( !empty($reviewID) ){
				$sql .= " AND $tblRating.review_ID=".esc_sql($reviewID);
			}else{
				$limit = absint($limit);
				$sql .= " ORDER BY $tblPosts.post_date DESC LIMIT $limit OFFSET $offset";
			}

			$oResult = $wpdb->get_results(
				$wpdb->prepare(
					$sql,
					$postID, 'review', 'publish'
				)
			);
		}else{
			$tblPostMeta  = $wpdb->prefix . 'postmeta';

			$sql = "SELECT $tblPosts.ID, $tblPosts.post_title, $tblPosts.post_date, $tblPosts.post_content, $tblRating.rating, $tblRating.user_ID, COALESCE(SUM($tblPostMeta.meta_value), 0) as thanks_score FROM $tblPosts LEFT JOIN $tblRating ON ($tblRating.review_ID = $tblPosts.ID) LEFT JOIN $tblPostMeta ON ($tblPostMeta.post_id=$tblPosts.ID) WHERE $tblRating.post_ID=%d AND $tblPosts.post_type=%s AND $tblPosts.post_status=%s GROUP BY $tblPosts.ID ORDER BY thanks_score DESC LIMIT $limit OFFSET $offset";

			$oResult = $wpdb->get_results(
				$wpdb->prepare(
					$sql,
					$postID, 'review', 'publish', self::$scoreThanksForReviewingKey
				)
			);
		}

		return $oResult;
	}

	public function fetchNewReviews(){
		if ( !isset($_POST['post_ID']) || empty($_POST['post_ID']) || !isset($_POST['paged']) || empty($_POST['paged']) || !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce','security', false) ){
			wp_send_json_error();
		}
		$orderBy = $_POST['orderBy'] === 'newest_first' ? 'post_date' : 'top_rating';
		$oResults = self::fetchReview($orderBy, 0, $_POST['post_ID'], $_POST['paged']);

		if ( empty($oResults) ){
			wp_send_json_error();
		}

		ob_start();
		foreach ( $oResults as $aResult ){
			self::renderReviewItem($aResult);
		}
		$content = ob_get_clean();
		wp_send_json_success(
			array(
				'review' => $content
			)
		);
	}
}