<?php
class WilokeAuthor{
	public static function getAuthorInfo($authorID, $isHidePrivateInfo=false){
		$aAuthor['link'] = get_author_posts_url($authorID);
		$aUserMeta = Wiloke::getUserMeta($authorID);
		$avatar = Wiloke::getUserAvatar($authorID, $aUserMeta, array(35, 35));

		$aAuthor['avatar'] = $avatar;
		$aAuthor['nickname'] = $aUserMeta['display_name'];
		$aAuthor['other'] = $aUserMeta;

		if ( strpos($avatar,'profile-picture.jpg') !== false ){
			$firstCharacter = strtoupper(substr($aAuthor['nickname'], 0, 1));
			$aAuthor['avatar_color'] = WilokePublic::getColorByAnphabet($firstCharacter);
			$aAuthor['user_first_character'] = $firstCharacter;
		}

		if ( $isHidePrivateInfo ){
			unset($aAuthor['other']['first_name']);
			unset($aAuthor['other']['last_name']);
			unset($aAuthor['other']['rich_editing']);
			unset($aAuthor['other']['comment_shortcuts']);
			unset($aAuthor['other']['use_ssl']);
			unset($aAuthor['other']['use_ssl']);
			unset($aAuthor['other']['show_admin_bar_front']);
			unset($aAuthor['other']['locale']);
			unset($aAuthor['other']['wp_capabilities']);
			unset($aAuthor['other']['wp_user_level']);
			unset($aAuthor['other']['dismissed_wp_pointers']);
			unset($aAuthor['other']['show_welcome_panel']);
			unset($aAuthor['other']['session_tokens']);
			unset($aAuthor['other']['community-events-location']);
			unset($aAuthor['other']['wp_user-settings']);
			unset($aAuthor['other']['wp_user-settings-time']);
			unset($aAuthor['other']['wp_user-settings-time']);
			unset($aAuthor['other']['nav_menu_recently_edited']);
			unset($aAuthor['other']['managenav-menuscolumnshidden']);
			unset($aAuthor['other']['metaboxhidden_nav-menus']);
			unset($aAuthor['other']['default_password_nag']);
			unset($aAuthor['other']['wp_dashboard_quick_press_last_post_id']);
			unset($aAuthor['other']['metaboxhidden_nav-menus']);
		}
		return $aAuthor;
	}
}