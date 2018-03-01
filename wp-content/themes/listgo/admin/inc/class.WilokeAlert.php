<?php
/**
 * WilokeAlert Class
 *
 * @category Alert
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

class WilokeAlert
{
    /**
     * Alert an error
     * @return string
     */
    public static function message($message, $isReturn=true, $isFrontEnd=true, $classAdditional=null)
    {
        if ($isFrontEnd)
        {
            if ( !current_user_can('edit_posts') )
            {
                return false;
            }
        }

        if ( $isReturn )
        {
            ob_start();
        }

        echo '<div class="wiloke-message-wrapper">';
            echo '<div class="wiloke-message-icon text-center">';
                echo '<img src="https://maps.gstatic.com/mapfiles/api-3/images/icon_error.png" />';
            echo '</div>';
            echo '<span class="wiloke-message text-center '.esc_attr($classAdditional).'">';
                echo wp_kses( $message, array(
                    'span'  => array(),
                    'a'     => array('href'=>array(), 'title'=>array()),
                    'strong'=> array()
                ) );
            echo '</span>';

            echo '<span class="wiloke-message text-center">'.esc_html__('Note that this message is only seen by the admin.', 'listgo').'</span>';

        echo '</div>';

        if ( $isReturn )
        {
            $content = ob_get_contents();
            ob_clean();

            return $content;
        }
    }

    /**
     * Render Alert
     */
    public static function render_alert($content='', $status='info', $isReturn = false, $isOnlySeenByAdmin=true)
    {
        if ( $isOnlySeenByAdmin ){
	       if ( !current_user_can('edit_theme_options') ){
	           return '';
           }
        }

        if ( is_admin() ) {
            $class = 'notice notice-'.str_replace('alert', 'error', $status);
        }else{
            $class = $status;
        }

	    if ( $isReturn )
	    {
		    ob_start();
	    }

        if ( is_admin() ) :
        ?>
        <div class="<?php echo esc_attr($class); ?>">
            <?php Wiloke::wiloke_kses_simple_html($content); ?>
        </div>
        <?php else:
        if ( !is_admin() && $isOnlySeenByAdmin ){
            $content .= esc_html__('Note that only admin can see this message.', 'listgo');
        }

        $icon = isset($aInfo['icon']) ? $aInfo['icon'] : 'icon_box-checked';
        ?>
        <div class="wil-alert wil-alert-has-icon alert-<?php echo esc_attr($class); ?>">
            <span class="wil-alert-icon"><i class="<?php echo esc_attr($icon); ?>"></i></span>
		    <?php if ( isset($aInfo['title']) ) : ?>
                <strong class="wil-alert-title"><?php echo esc_html($aInfo['title']); ?></strong>
		    <?php endif; ?>
            <p class="wil-alert-message"><?php \Wiloke::wiloke_kses_simple_html($content); ?></p>
		    <?php if ( isset($aInfo['can_remove']) ) : ?>
                <span class="wil-alert-remove" data-id="<?php echo esc_attr($aInfo['objectID']); ?>"></span>
		    <?php endif; ?>
        </div>
	    <?php
        endif;
	    if ( $isReturn )
	    {
		    $content = ob_get_contents();
		    ob_clean();

		    return $content;
	    }
    }
}
