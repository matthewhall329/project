/**
 * This is a part of Wiloke's Themes
 *
 * @website http://wiloke.com
 * @copyright Wiloke
 * @since 1.0
 */
;(function($){
    "use strict";

    $.fn.WilokeInfiniteScroll = function(params)
    {
        var oOptions = {
                totalAjaxLoaded         : 0,
                direction_enter         : 'down',
                direction_entered       : '',
                windowWidth             : $(window).outerWidth(),
                direction_exit          : '',
                direction_exited        : '',
                ajax_action             : 'wiloke_loadmore_portfolio',
                appendTo                : '.wiloke-items-store',
                max_posts               : 5,
                totalPostsOfTerm        : 0,
                currentFilterCssClass   : '.active',
                post_type               : '',
                itemCssClass            : '.item',
                navFiltersCssClass      : '.wiloke-nav-filter li',
                navFilterWrapperCssClass      : '.wiloke-nav-filter',
                btnClass                : '.wiloke-btn-infinite-scroll',
                progressingClass        : '.wiloke-progress-infinite-scroll',
                isInfiniteScroll        : true,
                containerClass      : '.wiloke-infinite-scroll-wrapper',
                additional          : {},
                is_debug            : false,
                afterAppended       : function () {},
                beforeAppend        : function ($this) { return $this; },
                currentTerm         : null
            },
            $self = $(this),
            $xhr,
            isDebug = false,
            wilokeInview;

        oOptions = $.extend({}, oOptions, params);

        if ( typeof oData !== 'undefined' )
        {
            oOptions = $.extend(oOptions, oData);
        }

        var woInfiniteScroll = {
            $el: $self,
            options: oOptions,
            post__not_in: null,
            $container: $self.closest(oOptions.containerClass),
            init: function () {
                var _this = this;
                _this.events();
                // Load more button only display if the loaded posts is smaller than total posts. But sometimes Term filter items are clicked and the posts in the term to be loaded. Then total posts in the terms equal to found posts.
            },
            events: function()
            {
                var _this = this;
                _this.onLoadmoreClick();
                _this.onNavFilterClick();

                if ( _this.options.isInfiniteScroll )
                {
                    _this.triggerInfiniteScroll();
                }
            },
            getLength: function () {
                var _this  = this,
                    filter = $(_this.options.navFilterWrapperCssClass, _this.$container).find(_this.options.currentFilterCssClass).data('filter');

                    // May be in a
                    if ( typeof filter == 'undefined' )
                    {
                        filter = $(_this.options.navFilterWrapperCssClass, _this.$container).find(_this.options.currentFilterCssClass).children().data('filter');
                    }

                if ( filter != '*' )
                {
                    return _this.$container.find(filter).length;
                }else{
                    return _this.$container.find(_this.options.itemCssClass).length;
                }
            },
            onNavFilterClick: function()
            {
                var _this = this;

                $(_this.options.navFiltersCssClass, _this.$container).on('click', function (event) {
                    event.preventDefault();

                    var $this           = $(this),
                        _filterClass    = $this.data('filter');
                        _this.options.currentTerm = $this.data('termid');
                        _this.options.totalPostsOfTerm = $this.data('total');

                    if (  typeof _filterClass == 'undefined' )
                    {
                        _filterClass = $this.children().data('filter');
                    }

                    // Loaded all posts of this term

                    if ( $this.data('is-loaded') )
                    {
                        _this.$container.find(_this.options.progressingClass).removeClass('loading');
                        $(_this.options.btnClass, _this.$container).attr('disabled', true);
                    }else{
                        // _this.$container.find(_this.options.progressingClass).addClass('loading');
                        $(_this.options.btnClass, _this.$container).attr('disabled', false);
                    }
                    // if there are no posts of the term, We will trigger load

                    if ( $(_this.options.appendTo).find(_filterClass+_this.options.itemCssClass).length < 1 )
                    {
                        $(_this.options.btnClass, _this.$container).trigger('click');

                        if ( $(_this.options.btnClass, _this.$container).data('only-one-time') == 'yes' )
                        {
                            $this.data('is-loaded', true);
                        }
                    }

                    if ( _filterClass == '*' )
                    {
                        if ( $(_this.options.itemCssClass, _this.$container).length == $(_this.options.btnClass, _this.$container).data('max_posts') )
                        {
                            $(_this.options.btnClass, _this.$container).remove();
                        }else{
                            $(_this.options.btnClass, _this.$container).removeClass('hidden');
                        }
                    }else{
                        if ( $(_filterClass, _this.$container).length >= _this.options.totalPostsOfTerm ){
                            $(_this.options.btnClass, _this.$container).addClass('hidden');
                        }else{
                            $(_this.options.btnClass, _this.$container).removeClass('hidden');
                        }
                    }
                })
            },
            onLoadmoreClick: function () {
                var _this = this, _currentPaged=1;
                $(_this.options.btnClass, _this.$container).on('click', function (event)
                {
                    event.preventDefault();

                    var  $this = $(this),
                        _currentTerm = _this.options.currentTerm,
                        _nonce       = $(this).data('nonce'),
                        isSingleTerm = false;
                    if ( ($xhr && $xhr.readyState !== 4) || $this.data('is-ajax') === true )
                    {
                        $(_this.options.progressingClass).removeClass('loading');
                        $(_this.options.progressingClass).addClass('loaded');
                        return false;
                    }

                    $(_this.options.progressingClass).addClass('loading');
                    $(_this.options.progressingClass).removeClass('loaded');

                    if ( !_this.options.is_debug ) {
                        $this.data('is-ajax', true);
                        $this.prop('disabled', true);
                    }

                    if ( typeof _currentTerm == 'undefined' || _currentTerm === null )
                    {
                        _currentTerm = $this.data('terms');
                    }else{
                        isSingleTerm = true;
                    }

                    _this.post__not_in = _this.post__not_in !== null ? _this.post__not_in : $this.attr('data-postids');

                    if ( (_this.post__not_in == null) || (typeof _this.post__not_in === 'undefined') )
                    {
                        _this.post__not_in = '';

                         _this.$container.find(_this.options.itemCssClass).each(function ()
                         {
                             if ( typeof $(this).data('id') != 'undefined' )
                             {
                                 _this.post__not_in += $(this).data('id') + ',';
                             }
                         });
                    }

                    $xhr = $.ajax({
                        method: 'POST',
                        url: WILOKE_GLOBAL.ajaxurl,
                        cache : true,
                        data: {
                            action              : _this.options.ajax_action,
                            totalAjaxLoaded     : _this.options.totalAjaxLoaded,
                            security            : _nonce,
                            term_ids            : _currentTerm,
                            totalPostsOfTerm    : _this.options.totalPostsOfTerm,
                            post__not_in        : _this.post__not_in,
                            number_of_loaded    : _this.getLength(),
                            max_posts           : $this.data('max_posts'),
                            post_type           : _this.options.post_type,
                            windowWidth         : _this.options.windowWidth,
                            additional          : _this.options.additional
                        },
                        success: function (response, status, xhr)
                        {
                            var postIDs = xhr.getResponseHeader('Wiloke-PostsNotIn');

                            if ( postIDs !== null )
                            {
                                if ( _this.post__not_in === null )
                                {
                                    _this.post__not_in = postIDs;
                                }else{
                                    _this.post__not_in = _this.post__not_in + ',' + postIDs;
                                }
                            }

                            if ( !response.success )
                            {
                                $(_this.options.progressingClass).toggleClass('loading');

                                if ( isSingleTerm ) {
                                    $this.data('is-ajax-' + _currentTerm, true);
                                }else{
                                    $this.remove();
                                    return;
                                }

                                $this.prop('disabled', false);
                            }

                            if ( response.success )
                            {
                                if ( isSingleTerm )
                                {
                                    $(_this.options.navFiltersCssClass, _this.$container).find(oOptions.currentFilterCssClass).data('is_loaded', true);
                                    $this.data('is-ajax-'+_currentTerm, true);
                                    $(_this.options.navFiltersCssClass, _this.$container).find(_this.options.currentFilterCssClass).data('is-loaded', true);
                                    $(_this.options.progressingClass).addClass('loading');
                                }else{
                                    if ( _this.options.isInfiniteScroll )
                                    {
                                        wilokeInview.destroy();
                                    }
                                }

                                $this.data('is-ajax', false);

                                if ( response.data == '' || !response.data )
                                {
                                    $this.remove();
                                    $(_this.options.progressingClass).addClass('loaded');
                                    return;
                                }
                            }

                            _currentPaged = response.data.next_page;

                            var _length     = Object.keys(response.data.data.item).length,
                                _count      = 1,
                                $renderItems = '';

                            _this.options.totalAjaxLoaded += _length;

                            $.each(response.data.data.item, function(index, value){

                                $renderItems += value;

                                var tempImg = new Image();

                                tempImg.src = $('img', $(value)).attr('src');

                                tempImg.onload = function () {
                                    if (_count == _length) {

                                        $renderItems = $($renderItems);
                                        $renderItems = _this.options.beforeAppend($renderItems);

                                        if ( $().isotope )
                                        {
                                            $(_this.options.appendTo, _this.$container).append($renderItems).isotope('appended', $renderItems);
                                            $(_this.options.appendTo, _this.$container).isotope('reloadItems').isotope({sortBy: 'original-order'});
                                        }else{
                                            $(_this.options.appendTo, _this.$container).append($renderItems).masonry('appended', $renderItems);
                                            $(_this.options.appendTo, _this.$container).masonry('reloadItems').masonry({sortBy: 'original-order'});
                                        }

                                        _this.options.afterAppended($this);

                                        $this.data('is-ajax', false);

                                        if ( _this.options.isInfiniteScroll === true )
                                        {
                                            wilokeInview.destroy();
                                            _this.triggerInfiniteScroll();
                                        }

                                        if ( response.data.finished == 'yes' ) {
                                            if ( !isSingleTerm ){
                                                $(_this.options.progressingClass).fadeOut('slow', function () {
                                                    $(_this.options.progressingClass).remove();
                                                });
                                                return;
                                            }else{
                                                $(_this.options.btnClass).addClass('hidden');
                                            }
                                        }

                                        if ( !$this.hasClass('mixed-loadmore-and-infinite-scroll') ){
                                            $(_this.options.progressingClass).toggleClass('loading loaded');
                                        }else{
                                            $(_this.options.progressingClass).removeClass('loaded');
                                            $(_this.options.progressingClass).addClass('loading');
                                        }

                                        $this.prop('disabled', false);
                                    }
                                    _count++;
                                };
                            });
                        }
                    })
                })
            },
            triggerInfiniteScroll: function () {
                var _this = this;

                wilokeInview = new Waypoint.Inview({
                    element: _this.$el[0],
                    enter: function (direction) {
                        if ( _this.options.direction_enter == direction )
                        {
                            $(_this.options.btnClass, _this.$container).trigger('click');
                        }
                    },
                    entered: function (direction) {
                        // if ( _this.options.direction_entered == direction )
                        // {
                        //     $(_this.options.btnClass, _this.$container).trigger('click');
                        // }
                    },
                    exit: function (direction) {
                        $(oOptions.btnClass, _this.$container).trigger('click');
                    },
                    exited: function (direction) {
                        if ( _this.options.direction_exited == direction )
                        {

                            //$(oOptions.btnClass, _this.$container).trigger('click');
                        }
                    }
                });
            }

        }

        woInfiniteScroll.init();
    }

})(jQuery);