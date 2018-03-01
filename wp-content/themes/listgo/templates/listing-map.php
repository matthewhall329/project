<?php
/*
 |--------------------------------------------------------------------------
 | Template name: Map Template
 |--------------------------------------------------------------------------
 |
 |
 */
get_header();
$aTemplateSettings = Wiloke::getPostMetaCaching($post->ID, 'map_template_settings');
echo do_shortcode('[wiloke_awesome_map source_map="all" extract_class="seciton-map" center="'.$aTemplateSettings['map_center'].'" map_theme="'.$aTemplateSettings['map_theme'].'" order_by="'.$aTemplateSettings['order_by'].'" order="'.$aTemplateSettings['order'].'"]');
get_footer();