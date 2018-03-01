<?php
/**
 * WilokeAjax Class
 *
 * @category Ajax
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

class WilokeAjax
{
    public $aAjaxFuncs = array();
    // register post types and taxonomy here.
    public function __construct()
    {
        $this->init();
    }

    /**
     * Adding ajax functions
     * true if you want to use no private function
     */
    public function init()
    {
        $aAjax = array(
            'ignore_intro'          => false,
            'loadmore_portfolio'    => true,
            'get_post_data'         => true,
            'loadmore_posts'        => true,
            'contactform7_demo'     => false,
            'get_posts_by_id'       => true,
            'rating_chart'          => true,
            'infinite_scroll'       => true,
            'polling'               => true
        );

        foreach ( $aAjax as $ajax => $isNoPriv )
        {
            add_action('wp_ajax_wiloke_'.$ajax, array(__CLASS__, 'wiloke_'.$ajax));

            if ( $isNoPriv )
            {
                add_action('wp_ajax_nopriv_wiloke_'.$ajax, array(__CLASS__, 'wiloke_'.$ajax));
            }
        }

    }

    /**
     * Ignore Intro Js
     * @since 1.0.1
     */
    public function wiloke_ignore_intro()
    {
        $aIgnore   = get_option('wiloke_ingore_intro');
        $aIgnore   = $aIgnore ? json_decode($aIgnore) : array();
        $aIgnore[$_POST['post_type']] = 'yes';
        $aIgnore   = json_encode($aIgnore);

        update_option('wiloke_ingore_intro', $aIgnore);
        die();
    }

    /**
     * Import Contact 7
     */
    static public function wiloke_contactform7_demo()
    {
        global $wiloke;
        
        if ( isset($wiloke->aConfigs['contactform7'][$_POST['import_of']]) )
        {
            print $wiloke->aConfigs['contactform7'][$_POST['import_of']];
        }else{
            esc_html_e('There are no contact forms', 'listgo');
        }

        die();
    }


    /**
     *
     */
    static public function wiloke_infinite_scroll()
    {
        if ( !check_ajax_referer('wiloke_portfolio_key', 'nonce', false) )
        {
            wp_die('404');
        }

        $aPostNotIn = array();

        $args = array(
            'post_type'   => 'gallery',
            'post_status' => 'publish'
        );

        if ( isset($_POST['post__not_in']) && !empty($_POST['post__not_in']) )
        {
            $_POST['post__not_in'] = rtrim($_POST['post__not_in'], ',');

            $args['post__not_in'] = explode(',', $_POST['post__not_in']);
            $args['post__not_in'] = array_map('intval', $args['post__not_in']);
        }

        if ( isset($_POST['term_ids']) && !empty($_POST['term_ids']) )
        {
            $args['tax_query'] = array(
              array(
                  'taxonomy' => 'gallery-category',
                  'field'    => 'term_id',
                  'terms'    =>  explode(',', $_POST['term_ids'])
              )
            );
        }

        if ( isset($_POST['max_posts']) && !empty($_POST['max_posts']) && absint($_POST['max_posts']) <= 20 )
        {
            $args['posts_per_page'] = $_POST['max_posts'];
        }else{
            $args['posts_per_page'] = 6;
        }

        $wilokeQuery = new WP_Query($args);

        if ( $wilokeQuery->have_posts() )
        {
            global $post;
            ob_start();
            while ( $wilokeQuery->have_posts() )
            {
                $wilokeQuery->the_post();
                $aPostNotIn[] = $post->ID;
                WilokeInfiniteScroll::render_item($post->ID, $post);
            }
            $contents = ob_get_contents();
            ob_end_clean();

            header('Wiloke-PostsNotIn: '.implode(',', $aPostNotIn));

            print $contents;

        }else{
            wp_send_json_success();
        }
        wp_reset_postdata();

        wp_die();

    }

    /**
     * Load more project
     */
    static public function wiloke_loadmore_portfolio()
    {
        if ( !isset($_POST['next_page']) || empty($_POST['next_page']) )
        {
            die('404');
        }

        if ( !isset($_POST['data']) || empty($_POST['data']) )
        {
            die('404');
        }

        if ( !isset($_POST['nonce']) || empty($_POST['nonce']) )
        {
            die('404');
        }

        if ( !isset($_POST['page_id']) || empty($_POST['page_id']) )
        {
            die('404');
        }

        if ( !check_ajax_referer('wiloke_portfolio_key', 'nonce', false) )
        {
            die('404');
        }

        $aData = stripslashes($_POST['data']);

        $aData = json_decode($aData, true);

        if ( empty($aData) )
        {
            die('404');
        }


        if ( isset($_POST['term_ids']) && !empty($_POST['term_ids']) )
        {
            $aData['term_ids'] = stripslashes($_POST['term_ids']);
        }

        if ( !empty($_POST['post__not_in']) )
        {
            $_POST['post__not_in'] = stripslashes($_POST['post__not_in']);
            $aData['post__not_in'] = explode(',', $_POST['post__not_in']);
            $aData['post__not_in'] = array_unique($aData['post__not_in']);
        }

        $aData['posts_per_page']  = self::wiloke_parse_posts_per_page($aData);

        $aData['is_get_terms'] = false;

        $pageID = intval($_POST['page_id']);

        if ( current_user_can('edit_posts') )
        {
            $aData['is_frontend_edit'] = true;
        }

        $jsonContent = self::wiloke_get_post_data('portfolio', $aData, $pageID);

        print $jsonContent;

        die();
    }

    /**
     * Change project size
     */
    static public function wiloke_save_project_size()
    {
        if ( !isset($_POST['data']) || empty($_POST['data']) )
        {
            die('404');
        }

        $_POST['data'] = stripslashes($_POST['data']);

        $aParseData = json_decode($_POST['data'], true);

        if ( !isset($aParseData['id']) || empty($aParseData['id']) )
        {
            die('404');
        }

        if ( !isset($aParseData['page_id']) || empty($aParseData['page_id']) )
        {
            die('404');
        }

        $postID = intval($aParseData['id']);
        $pageID = intval($aParseData['page_id']);

        unset($aParseData['id']);
        unset($aParseData['page_id']);

        $aParseData['wiloke_portfolio_size']         = isset($aParseData['wiloke_portfolio_size']) && !empty($aParseData['wiloke_portfolio_size']) ? stripslashes($aParseData['wiloke_portfolio_size']) : 'full';
        $aParseData['wiloke_portfolio_customsize']   = isset($aParseData['wiloke_portfolio_customsize']) ? stripslashes($aParseData['wiloke_portfolio_customsize']) : '';


        $jsonData = json_encode($aParseData);

        $key = Wiloke::wiloke_parse_template_to_key($pageID);

        $key = $key . '_wiloke_project_size';

        $aPostMeta = get_post_meta($pageID, $key, true);
        $aPostMeta[$postID] = $jsonData;

        update_post_meta($pageID, $key, $aPostMeta);

        if ( $aParseData['wiloke_portfolio_size'] == 'custom' )
        {
            $size = $aParseData['wiloke_portfolio_customsize'];
        }else{
            $size = $aParseData['wiloke_portfolio_size'];
        }

        if ( $key == 'template-portfolio3_wiloke_project_size' )
        {
            $content  = $size;
            $style    = 'project-3';
        }elseif( $key == 'template-portfolio4_wiloke_project_size' )
        {
            // $size    = wiloke_venus_project_size($size);
            $content = get_the_post_thumbnail($postID, $size);
            $style   = 'project-4';
        }elseif( $key == 'template-portfolio1_wiloke_project_size' )
        {
            // $size    = wiloke_venus_project_size($size);
            $content = get_the_post_thumbnail($postID, $size);

            $style   = 'project-1';
        }

        $res = array('style'=>$style, 'content'=>$content, 'status'=>'200');
        $res = json_encode($res);

        die($res);
    }

    /**
     * Get Posts
     */
    static public function wiloke_loadmore_posts($postType='', $aArgs=array(), $isReturn=false)
    {
        if ( check_ajax_referer('security', 'wiloke_ajax_nonce', false) ) {
            wp_send_json_error();
        }
        
        $isAjax = true;
        $aPostNotIn = array();

        if ( empty($postType) || empty($aArgs) )
        {
            if ( isset($_POST['post_type'])  )
            {
                $postType = sanitize_text_field($_POST['post_type']);
                $aArgs    = $_POST;
            }
        }else{
            $isAjax = false;
        }

        $args = array(
            'post_types'    => $postType,
            'paged'         => 1,
            'posts_per_page'=> $aArgs['additional']['general_settings']['number_of_posts']
        );

        $aArgs  = apply_filters('wiloke_ajax_load_posts_input_args', $aArgs);
        $args   = wp_parse_args($aArgs, $args);

        if ( isset($aArgs['term_ids']) && !empty($aArgs['term_ids']) )
        {
            $aArgs['term_ids']  = trim($aArgs['term_ids'], ',');
            $parseTerms         = explode(',', $aArgs['term_ids']);

            $args['tax_query']  = array(
                array(
                    'taxonomy' => $aArgs['taxonomy'],
                    'field'    => 'term_id',
                    'terms'    => $parseTerms
                )
            );
        }

        $args = Wiloke::wiloke_query_args($args);
        $args = apply_filters('wiloke_ajax_load_posts_args', $args, $aArgs);

        $wilokeQuery = new WP_Query($args);

        $aResponse   = array();

        $nextPage = intval($aArgs['paged']) + 1;

        if ( $nextPage > $wilokeQuery->max_num_pages )
        {
            $nextPage = -1;
        }

        if ( isset($args['post__not_in']) && !empty($args['post__not_in']) )
        {
            $aArgs['post__not_in']   = trim($aArgs['post__not_in'], ',');
            $aPostNotIn              = explode(',', $aArgs['post__not_in']);
            $aPostNotIn              = array_map('intval', $aPostNotIn);
        }

        if ( $wilokeQuery->have_posts() ) :
            $aResponse['before_item'] = apply_filters('wiloke_ajax_before_loop_'.$postType, $aArgs);
            global $post; $i = $wilokeI = 0;
            $wilokeI = apply_filters('wiloke_ajax_design_order_args_'.$postType, $wilokeI, $aArgs);

            while ( $wilokeQuery->have_posts() ) :
                $wilokeQuery->the_post();

                if ( has_filter('wiloke_ajax_filter_query_'.$postType) )
                {
                    $aResponse['item'][$i] = apply_filters('wiloke_ajax_filter_query_'.$postType, $aResponse['item'][$i], $post, $aArgs, $wilokeI);
                }else{
                    $aResponse[$i]['title'] = get_the_title($post->ID);

                    if ( has_post_thumbnail($post->ID) )
                    {
                        $aResponse[$i]['thumbnail'] = wp_get_attachment_thumb_url( get_post_thumbnail_id($post->ID) );
                    }else{
                        $aResponse[$i]['thumbnail'] = '';
                    }
                }

                $aResponse['post_id'][$i] = $post->ID;
                $aPostNotIn[] = $post->ID;

                $wilokeI++;
                $i++;
            endwhile;
            $aResponse['after_item'] = apply_filters('wiloke_ajax_after_loop_'.$postType, $aArgs);
        endif; wp_reset_postdata();

        if ( !empty($aResponse) )
        {
            $resJson = array('data' => $aResponse, 'status'=>200, 'next_page'=>$nextPage);
            if ( isset($aArgs['term_ids']) && !empty($aArgs['term_ids']) ){
                $maxPosts = absint($_POST['totalPostsOfTerm']);
            }else{
                $maxPosts = absint($_POST['max_posts']);
            }

            if ( ( absint($_POST['number_of_loaded']) + absint($args['posts_per_page']) ) >= $maxPosts )
            {
                $resJson['finished'] = 'yes';
            }else{
                $resJson['finished'] = 'no';
            }
        }else{
            $resJson = false;
        }

        if ( $isAjax )
        {
            header('Wiloke-PostsNotIn: '.implode(',', $aPostNotIn));
            wp_send_json_success($resJson);
            wp_die();
        }else{
            if ( $isReturn )
            {
                echo json_encode($resJson);
            }
        }
    }

    /**
     * Get posts per page
     */
    static public function wiloke_parse_posts_per_page($aAtts)
    {
        if ( !empty($aAtts['amount_of_loadmore']) )
        {
            $postsPerPage = $aAtts['amount_of_loadmore'];
        }else{
            if ( isset($aAtts['number_of_columns']) && isset($aAtts['number_of_rows']) )
            {
                $postsPerPage = intval($aAtts['number_of_columns']) * intval($aAtts['number_of_rows']);
            }else{
                $postsPerPage = intval($aAtts['posts_per_page']);
            }
        }

        if ( $postsPerPage < 1 )
        {
            $postsPerPage = 1;
        }

        return $postsPerPage;
    }

    /**
     * Get the data of post. In this function, @post_type is required.
     */
    public static function wiloke_get_post_data($postType='', $aArgs=array(), $pageID = '')
    {
        global $post; $isAjax = true; $aMaxPosts = array();

        if ( empty($pageID) )
        {
            $pageID = $post->ID;
        }

        if ( empty($postType) || empty($aArgs) )
        {
            if ( !isset($_POST['post_type']) || empty($_POST['post_type']) || !isset($_POST['args']) || empty($_POST['args']) )
            {
                die();
            }else{
                $postType = $_POST['post_type'];
                $aArgs    = $_POST['args'];
            }
        }else{
            $isAjax = false;
        }

        $args = array(
           'post_types' => $postType,
           'paged'     => 1
        );

        $args = wp_parse_args($aArgs, $args);

        if ( isset($aArgs['term_ids']) && !empty($aArgs['term_ids']) )
        {
            if ( $postType != 'post' )
            {
                $aArgs['term_ids'] = is_array($aArgs['term_ids']) ? $aArgs['term_ids'] : explode(',', $aArgs['term_ids']);

                $args['tax_query'] = array(
                    array(
                        'taxonomy'   => $aArgs['taxonomy'],
                        'field'      => 'term_id',
                        'terms'      => $aArgs['term_ids']
                    )
                );

            }else{
                $args['category__in'] = $aArgs['taxonomy_ids'];
            }

        }else{
            if ( !isset($aArgs['taxonomy']) )
            {
                $aArgs['taxonomy']    = 'category';
            }
        }

        $args = Wiloke::wiloke_query_args($args);

        $wilokeQuery = new WP_Query($args);

        $aTaxonomies = array();
        $aResponse   = array();
        $aPostIDs    = array();

        if ( isset($aArgs['paged']) )
        {
            $nextPage = intval($aArgs['paged']) + 1;
        }else{
            $nextPage = 1;
        }

        $aArgs['thumbnail_size'] = isset($aArgs['thumbnail_size']) ?  Wiloke::wiloke_parse_thumbnail_size($aArgs['thumbnail_size']) : 'thumbnail';

        if ( $wilokeQuery->have_posts() ) :

            $aTaxonomies = array();
            $aMaxPosts['all']   = $wilokeQuery->found_posts;

            if ( !isset($aArgs['is_get_terms']) || $aArgs['is_get_terms'] === true )
            {
                if (isset($aArgs['term_ids']) && !empty($aArgs['term_ids'])) {
                    foreach ($aArgs['term_ids'] as $id) {
                        $aGetTax = get_term($id, $aArgs['taxonomy']);

                        if ( !empty($aGetTax) && !is_wp_error($aGetTax) )
                        {
                            $aTaxInfo = array(
                                'term_id' => $aGetTax->term_id,
                                'name' => $aGetTax->name,
                                'slug' => $aGetTax->slug
                            );

                            $aTaxonomies[$aGetTax->term_id] = $aTaxInfo;
                            $aMaxPosts[$aGetTax->term_id] = $aGetTax->count;
                        }
                    }
                } else {
                    $aGetTaxs = get_terms($aArgs['taxonomy']);

                    if (!empty($aGetTaxs) && !is_wp_error($aGetTaxs)) {
                        foreach ($aGetTaxs as $aGetTax) {
                            $aTaxInfo = array(
                                'term_id'    => $aGetTax->term_id,
                                'name'       => $aGetTax->name,
                                'slug'       => $aGetTax->slug
                            );

                            $aTaxonomies[$aGetTax->term_id] = $aTaxInfo;

                            $aMaxPosts[$aGetTax->term_id] = $aGetTax->count;
                        }
                    }
                }
            }


            $wilokeI = 0;

            while ( $wilokeQuery->have_posts() ) :

                $wilokeQuery->the_post();

                // get taxonomies. You can use this data to do a filter menu
                $aGetTaxs = Wiloke::wiloke_get_terms_by_post_id($post->ID, $aArgs['taxonomy']);
                $aPostIDs[] = $post->ID;

                $aTermsOfPost = array();

                if ( !empty($aGetTaxs) )
                {
                    foreach ( $aGetTaxs as $aGetTax )
                    {
                        $aTaxInfo = array(
                            'term_id' => $aGetTax->term_id,
                            'name'    => $aGetTax->name,
                            'slug'    => $aGetTax->slug
                        );

                        $aTermsOfPost[] = $aTaxInfo;
                    }
                }

                if ( isset($aArgs['filter_func']) )
                {
                    if ( has_filter($aArgs['filter_func'].'_get_data') )
                    {
                        $aPostMeta = apply_filters($aArgs['filter_func'].'_get_data', $post, $pageID, $aArgs);
                    }else{
                        $aPostMeta = array();
                    }

                    $aResponse[$wilokeI] = apply_filters($aArgs['filter_func'], $post, $aArgs, $aTermsOfPost, $pageID, $aPostMeta);

                }else{
                    $aResponse[$wilokeI]['title'] = get_the_title($post->ID);

                    if ( has_post_thumbnail($post->ID) )
                    {
                        $aResponse[$wilokeI]['thumbnail'] = get_the_post_thumbnail($post->ID, $aArgs['thumbnail_size']);
                    }
                }

                $wilokeI++;
            endwhile;

        endif; wp_reset_postdata();

        if ( !empty($aResponse) )
        {
            // Hide load more button
            $calMaxPosts = 0;

            if ( isset($_POST['max_posts']) && !empty($_POST['max_posts']) )
            {
                $aMaxPosts = $_POST['max_posts'];
            }

            if ( isset($aArgs['term_ids']) && !empty($aArgs['term_ids']) && (count($aArgs['term_ids']) < 2 ) )
            {
                foreach ( $aArgs['term_ids'] as $termID )
                {
                    $calMaxPosts = $calMaxPosts +  $aMaxPosts[$termID];
                }
            }else{
                $calMaxPosts = $aMaxPosts['all'];
            }

            if ( !empty($_POST['number_of_loaded']) )
            {
                // That's in the case of loadmore

                if ( ($_POST['number_of_loaded'] + $aArgs['posts_per_page']) >= ($calMaxPosts) )
                {
                    $nextPage = -1;
                }
            }else{
                // at the first time
                if ( $aArgs['posts_per_page'] == $calMaxPosts )
                {
                    $nextPage = -1;
                }
            }
            // end hide load more button

            $resJson = array('data' => $aResponse, 'post_ids' => $aPostIDs, 'taxonomies' => $aTaxonomies, 'status'=>200, 'next_page'=>$nextPage, 'max_posts'=>$aMaxPosts);
            $resJson = json_encode($resJson);
        }else{
            $resJson = json_encode( array('status'=>404) );
        }

        if ( $isAjax )
        {
            die($resJson);
        }else{
            return $resJson;
        }
    }

    static public function wiloke_get_posts_by_id()
    {
        check_ajax_referer( 'wiloke-get-post-by-ids-nonce', 'security' );

        if ( isset($_POST['post_ids']) && !empty($_POST['post_ids']) )
        {
            $aParsePostIDs = explode(',', $_POST['post_ids']);
            ?>
            <div class="post-related__owl owl__nav-middle">
                <?php
                    foreach ( $aParsePostIDs as $postID )
                    {
                        $postLink = get_permalink($postID);
                        if ( $postLink ) :
                        ?>
                        <a href="<?php echo esc_url(get_permalink($postID)); ?>" class="post-related__item">
                            <div class="post-related__item-img">
                                <?php
                                    if ( has_post_thumbnail($postID) )
                                    {
                                        echo get_the_post_thumbnail($postID, 'hermes_500');
                                    }
                                ?>
                            </div>

                            <h4 class="post-related__item-title"><?php echo get_the_title($postID); ?></h4>
                        </a>
                        <?php
                        endif;
                    }
                ?>
            </div>
            <?php
        }

        wp_die();
    }
}
