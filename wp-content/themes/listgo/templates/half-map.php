<?php
/*
 |--------------------------------------------------------------------------
 | Template name: Half-Map Template
 |--------------------------------------------------------------------------
 |
 |
 */
get_header();
$aMapSettings = Wiloke::getPostMetaCaching($post->ID, 'half_map_settings');

echo do_shortcode('[wiloke_half_map source_map="all" max_zoom="'.$aMapSettings['maxZoom'].'" min_zoom="'.$aMapSettings['minZoom'].'" center_zoom="'.$aMapSettings['centerZoom'].'" max_cluster_radius="'.$aMapSettings['maxClusterRadius'].'" posts_per_page="'.$aMapSettings['posts_per_page'].'" image_size="'.$aMapSettings['image_size'].'" extract_class="seciton-map" show_terms="'.$aMapSettings['show_terms'].'"]');
get_footer();