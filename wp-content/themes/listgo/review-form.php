<?php
global $wiloke, $post;
$aResults = WilokeReview::fetchReview();
$totalReviews = 0;
$limit = get_option('comments_per_page');
?>
<div id="comments" class="comments">
    <?php
    if ( !empty($aResults) ) :
	    $aAverages  = WilokeReview::calculateRating();
        $totalReviews = $aAverages['number_of_ratings'];
	    $totalReviews = $totalReviews > 9 ? $totalReviews : '0' . $totalReviews;
        $prefix = $totalReviews > 1 ? esc_html__('Reviews', 'listgo') : esc_html__('Review', 'listgo');
    ?>
    <div class="comments__header">
        <h4 class="comment__title">
            <?php echo esc_html($totalReviews) . ' ' .  esc_html($prefix); ?>
            <a href="#comment-respond" class="comments__header-create"><?php esc_html_e('Write A Review', 'listgo'); ?></a>
        </h4>

        <select id="comments_orderby" name="comments_orderby" class="comments__header-order">
            <option value="newest_first"><?php esc_html_e('Newest First', 'listgo'); ?></option>
            <option value="top_review"><?php esc_html_e('Top Reviews', 'listgo'); ?></option>
        </select>

    </div>
    <ul class="review-rating">
        <?php WilokeReview::averageRating($aAverages); ?>
        <?php
            for ($score=5;$score>0;$score--){
                WilokeReview::diagramLineStars($aAverages, $score);
            }
        ?>
    </ul>
    <?php endif; ?>
    <ol id="commentlist" class="commentlist" data-totalcomments="<?php echo esc_attr($totalReviews); ?>" data-commentsperpage="<?php echo esc_attr($limit); ?>">
        <?php
            foreach ( $aResults as $oResult ){
	            WilokeReview::renderReviewItem($oResult);
            }
        ?>
    </ol>
    <div id="pagination-placeholder" class="nav-links text-center"></div>

</div>

<?php
if ( is_user_logged_in() ){
    if ( WilokeReview::isReviewed(get_current_user_id(), $post->ID) ){
        return '';
    }
}
?>

<div id="comment-respond" class="comment-respond">
	<h3 class="comment-reply-title"><?php esc_html_e('Write a Review', 'listgo'); ?></h3>
	<div class="row">
		<form id="form-rating" action="#" class="comment-form" enctype="multipart/form-data" method="POST">
            <input type="hidden" name="post_ID" value="<?php echo esc_attr($post->ID) ?>">
            <?php wp_nonce_field('wiloke-listgo-nonce-action', 'wiloke-listgo-nonce-field'); ?>

            <p class="col-sm-12">
                <label><?php esc_html_e('Your overall rating of this property', 'listgo'); ?> <sup>*</sup></label>
                <span class="comment__rate-wrap">
                    <span class="comment__rate">
                        <span class="selected">
                            <a class="fa fa-star-o" data-score="1" data-title="<?php esc_html_e('Terrible', 'listgo') ?>"></a>
                            <a class="fa fa-star-o" data-score="2" data-title="<?php esc_html_e('Poor', 'listgo') ?>"></a>
                            <a class="fa fa-star-o" data-score="3" data-title="<?php esc_html_e('Average', 'listgo') ?>"></a>
                            <a class="fa fa-star-o" data-score="4" data-title="<?php esc_html_e('Very Good', 'listgo') ?>"></a>
                            <a class="fa fa-star-o active" data-score="5" data-title="<?php esc_html_e('Excellent', 'listgo') ?>"></a>
                        </span>
                        <span class="comment__rate-placeholder" data-placeholder="<?php esc_html_e('Click to rate', 'listgo') ?>"><?php esc_html_e('Click to rate', 'listgo') ?></span>
                    </span>
                </span>
                <input type="hidden" name="rating" id="rating" value="5" required>
            </p>

			<?php if ( empty(WilokePublic::$oUserInfo) ) : ?>
                <p class="col-sm-12 form-item">
                    <label for="username"><?php esc_html_e('Username', 'listgo'); ?> <sup>*</sup></label>
                    <input id="username" name="username" type="text" required aria-required="true">
                </p>
				<p class="col-sm-12 form-item">
					<label for="email"><?php esc_html_e('Email', 'listgo'); ?> <sup>*</sup></label>
					<input id="email" name="email" type="text" required aria-required="true">
				</p>
                <p class="col-sm-12 form-item">
                    <label for="password"><?php esc_html_e('Password', 'listgo'); ?> <sup>*</sup></label>
                    <input id="password" name="password" type="password" required aria-required="true">
                </p>
			<?php endif; ?>

			<p class="col-sm-12">
				<label for="title"><?php esc_html_e('Title of your review', 'listgo'); ?> <sup>*</sup></label>
				<input id="title" name="title" type="text" required aria-required="true" placeholder="<?php esc_html_e('Summarize the service or highlight an interesting detail', 'listgo'); ?>">
			</p>

			<p class="col-sm-12">
				<label for="review"><?php esc_html_e('Your Review', 'listgo'); ?> <sup>*</sup></label>
				<textarea id="review" name="review" rows="5" cols="10" placeholder="<?php esc_html_e('Describe your experience', 'listgo'); ?>" required></textarea>
			</p>
            <?php if ( $wiloke->aThemeOptions['listing_toggle_add_photo_in_review_tab'] === 'enable' ) : ?>
            <div class="col-sm-12">
                <?php if ( current_user_can('upload_files') ) : ?>
                    <div class="wil-addlisting-gallery">
                        <ul id="wiloke-preview-gallery" class="wil-addlisting-gallery__list">
                            <li id="wiloke-listgo-add-gallery" class="wil-addlisting-gallery__placeholder" title="<?php esc_html_e('Upload Gallery', 'listgo'); ?>">
                                <button data-multiple="true" class="wiloke-js-upload"><i class="icon_image"></i></button>
                            </li>
                        </ul>
                    </div>
                    <input type="hidden" id="upload_photos" class="wiloke-insert-id is-using-media" name="upload_photos" value="">
                <?php else: ?>
                    <div id="preview-gallery" class="comment__gallery" data-allow="<?php echo esc_attr(str_replace('M', '', WilokePublic::getMaxFileSize())); ?>"></div>
                    <label for="upload_photos" class="input-upload-file">
                        <input id="upload_photos" type="file" name="upload_photos[]" multiple>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg>
                        <span><?php esc_html_e('Add a photo', 'listgo'); ?></span>
                        <i><?php Wiloke::wiloke_kses_simple_html(sprintf(__('The image size should smaller than or equal to %s ', 'listgo'), WilokePublic::getMaxFileSize())); ?></i>
                    </label>
                <?php endif; ?>
            </div>
            <?php endif; ?>

			<?php do_action('wiloke/review-form/before-submit-button'); ?>

			<p class="col-sm-12">
				<button id="submit-review" class="listgo-btn btn-primary listgo-btn--large" type="submit"><?php echo empty(WilokePublic::$oUserInfo) ? esc_html__('Signup & Submit Review', 'listgo') :  esc_html__('Submit Review', 'listgo'); ?></button>
                <span class="review_status success-msg"></span>
			</p>
		</form>
	</div>
</div>