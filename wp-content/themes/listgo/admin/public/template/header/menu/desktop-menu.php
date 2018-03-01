<?php
global $wiloke;
$key = $wiloke->aConfigs['frontend']['register_nav_menu']['menu'][0]['key'];
if ( has_nav_menu($key) ) :
    ?>
    <nav class="header__nav">
        <?php wp_nav_menu($wiloke->aConfigs['frontend']['register_nav_menu']['config'][$key]); ?>
    </nav>
    <?php
endif;
?>